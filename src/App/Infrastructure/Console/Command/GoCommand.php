<?php

namespace App\Infrastructure\Console\Command;

use App\Domain\Fixture;
use App\Infrastructure\Configuration\YamlFileLoader;
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
use Twig\Loader\FilesystemLoader;

class GoCommand extends Command
{
    protected static $defaultName = 'go';

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
            'Specify the output file, defaults to CWD.'
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

        $twig = new Environment(
            new FilesystemLoader([
                __DIR__ . '/../../../Resources/templates'
            ]),
            [
                'autoescape' => false,
            ]
        );

        (new Fixture\Command\ExtractLocales())(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/locales.yml', 'w'),
            $config['locales']
        );

        $style->writeln('locales <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractAttributes($pdo, $twig))(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/attributes.csv', 'w'),
            $config['attributes'],
            array_keys($config['locales'])
        );

        $style->writeln('attributes <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractAttributeOptions($pdo, $twig))(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/attribute_options.csv', 'w'),
            array_keys($config['attributes']),
            array_keys($config['locales']),
            $config['codes_mapping']
        );

        $style->writeln('attribute options <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractAttributeGroups())(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/attribute_groups.csv', 'w'),
            $config['groups'],
            array_keys($config['locales'])
        );

        $style->writeln('attribute groups <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractFamilies())(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/families.csv', 'w'),
            $config['families'],
            $config['scopes'],
            array_keys($config['locales'])
        );

        $style->writeln('families <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractFamilyVariants())(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/family_variants.csv', 'w'),
            $config['families'],
            array_keys($config['locales'])
        );

        $style->writeln('family variants <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        (new Fixture\Command\ExtractSimpleProducts($pdo, $twig))(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/products.csv', 'w'),
            $config['families'],
            array_keys($config['locales'])
        );

        $style->writeln('products <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);
        $style->writeln('product models <fg=red>nok</>', SymfonyStyle::OUTPUT_PLAIN);

        return 0;
    }
}