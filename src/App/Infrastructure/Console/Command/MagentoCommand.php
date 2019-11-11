<?php

namespace App\Infrastructure\Console\Command;

use App\Domain\Fixture;
use App\Domain\Magento\Attribute;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\Family;
use App\Domain\Magento\Locale;
use App\Domain\Magento\Scope;
use App\Domain\Magento\SqlExportTwigExtension;
use App\Infrastructure\AttributeRendererFactory;
use App\Infrastructure\Configuration\YamlFileLoader;
use App\Infrastructure\FamiliesFactory;
use App\Infrastructure\Normalizer\Magento\AttributeDenormalizerFactory;
use App\Infrastructure\Normalizer\Magento\LocaleDenormalizerFactory;
use App\Infrastructure\Normalizer\Magento\ScopeDenormalizerFactory;
use App\Infrastructure\VariantAxisesFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class MagentoCommand extends Command
{
    protected static $defaultName = 'magento';

    private $logger;

    public function __construct(string $name = null, LoggerInterface $logger = null)
    {
        parent::__construct($name);
        $this->logger = $logger ?? new NullLogger();
    }

    protected function configure()
    {
        $this->setDescription('Generate the fixtures files depending on your catalog.yaml configuration and your Magento data.');

        $this->addOption(
            'dsn',
            'd',
            InputOption::VALUE_OPTIONAL,
            'Specify a Data Source Name for PDO, defaults to APP_DSN environment variable.'
        );

        $this->addOption(
            'username',
            'u',
            InputOption::VALUE_OPTIONAL,
            'Specify the username for PDO, defaults to APP_USERNAME environment variable.'
        );

        $this->addOption(
            'password',
            'p',
            InputOption::VALUE_OPTIONAL,
            'Specify the password for PDO, defaults to APP_PASSWORD environment variable.'
        );

        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Specify the path to the catalog config file.'
        );

        $this->addArgument(
            'output',
            InputArgument::OPTIONAL,
            'Specify the output path, defaults to CWD.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locator = new FileLocator([
            getcwd(),
        ]);

        $loader = new DelegatingLoader(new LoaderResolver([
            new GlobFileLoader($locator),
            new YamlFileLoader($locator)
        ]));

        $style = new SymfonyStyle($input, $output);

        try {
            if (!empty($file = $input->getOption('config'))) {
                $config = $loader->load($file, 'glob');
            } else {
                $config = $loader->load('{.,}catalog.y{a,}ml', 'glob');
            }
        } catch (LoaderLoadException $e) {
            $style->error($e->getMessage());
            return -1;
        }

        try {
            $pdo = new \PDO(
                $input->getOption('dsn') ?? $_ENV['APP_DSN'] ?? 'mysql:host=localhost;dbname=magento',
                $input->getOption('username') ?? $_ENV['APP_USERNAME'] ?? 'root',
                $input->getOption('password') ?? $_ENV['APP_PASSWORD'] ?? null,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]
            );
        } catch (\PDOException $e) {
            $style->error($e->getMessage());
            $style->error(strtr('Connection parameters were: %dsn% with user %user%, using password: %password%.', [
                '%dsn%' => $input->getOption('dsn') ?? $_ENV['APP_DSN'] ?? 'mysql:host=localhost;dbname=magento',
                '%user%' => $input->getOption('username') ?? $_ENV['APP_USERNAME'] ?? 'root',
                '%password%' => ($input->getOption('password') ?? $_ENV['APP_PASSWORD'] ?? null) !== null ? 'Yes' : 'No',
            ]));
            return -1;
        }

        $twig = new Environment(
            new FilesystemLoader([
                __DIR__ . '/../../../Resources/templates'
            ]),
            [
                'autoescape' => false,
                'debug' => true,
            ]
        );

        $twig->addExtension(new DebugExtension());
        $twig->addExtension(new SqlExportTwigExtension());

        /** @var Attribute[] $attributes */
        $attributes = (new AttributeDenormalizerFactory())()
            ->denormalize($config['attributes'], Attribute::class.'[]');
        /** @var Scope[] $scopes */
        $scopes = (new ScopeDenormalizerFactory())()
            ->denormalize($config['scopes'], Scope::class.'[]');
        /** @var Locale[] $locales */
        $locales = (new LocaleDenormalizerFactory())()
            ->denormalize($config['locales'], Locale::class.'[]');

        $axisAttributes = array_filter($attributes, function (Attribute $renderer) use ($config) {
            $code = $renderer->code();
            foreach ($config['families'] as $family) {
                foreach ($family['variations'] as $variation) {
                    if (in_array($code, $variation['level_1']['axis']) ||
                        (isset($variation['level_2']['axis']) && in_array($code, $variation['level_2']['axis']))
                    ) {
                        return true;
                    }
                }
            }

            return false;
        });

        /** @var AttributeRenderer[] $attributeRenderers */
        $attributeRenderers = (new AttributeRendererFactory())(
            $attributes,
            $axisAttributes,
            $scopes,
            $locales,
            $config['attributes']
        );

        /** @var Family[] $families */
        $families = (new FamiliesFactory(...$attributeRenderers))($config);
        /** @var Attribute[] $axises */
        $axises = (new VariantAxisesFactory(...$attributeRenderers))($config);

        (new Fixture\Command\ExtractLocales())(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/locales.yml', 'x'),
            $config['locales']
        );

        $style->writeln('locales <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractChannels())(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/channels.csv', 'x'),
            $config['scopes'],
            $config['locales']
        );

        $style->writeln('channels <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractAttributes($pdo, $twig, $this->logger))(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/attributes.csv', 'x'),
            $attributeRenderers,
            $locales,
            $config['codes_mapping']
        );

        $style->writeln('attributes <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractAttributeOptions($pdo, $twig, $this->logger))(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/attribute_options.csv', 'x'),
            array_keys($config['attributes']),
            array_keys($config['locales']),
            $config['codes_mapping']
        );

        $style->writeln('attribute options <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractAttributeGroups())(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/attribute_groups.csv', 'x'),
            $config['groups'],
            array_keys($config['locales'])
        );

        $style->writeln('attribute groups <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractFamilies())(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/families.csv', 'x'),
            $config['families'],
            $config['scopes'],
            array_keys($config['locales'])
        );

        $style->writeln('families <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractFamilyVariants())(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/family_variants.csv', 'x'),
            $config['families'],
            array_keys($config['locales'])
        );

        $style->writeln('family variants <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractProducts($pdo, $twig, $this->logger))(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/products.csv', 'x'),
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/product_models.csv', 'x'),
            $attributeRenderers,
            $families,
            $axises,
            $config['codes_mapping']
        );

        $style->writeln('products and product models <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        return 0;
    }
}