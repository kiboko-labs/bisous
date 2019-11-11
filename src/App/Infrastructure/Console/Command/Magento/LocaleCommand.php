<?php

namespace App\Infrastructure\Console\Command\Magento;

use App\Domain\Fixture;
use App\Domain\Magento\Locale;
use App\Infrastructure\Configuration\YamlFileLoader;
use App\Infrastructure\Normalizer\Magento\LocaleDenormalizerFactory;
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

class LocaleCommand extends Command
{
    protected static $defaultName = 'magento:locales';

    private $logger;

    public function __construct(string $name = null, LoggerInterface $logger = null)
    {
        parent::__construct($name);
        $this->logger = $logger ?? new NullLogger();
    }

    protected function configure()
    {
        $this->setDescription('Generate the locales fixtures files depending on your catalog.yaml configuration and your Magento data.');

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

        /** @var Locale[] $locales */
        $locales = (new LocaleDenormalizerFactory())()
            ->denormalize($config['locales'], Locale::class.'[]');

        (new Fixture\Command\ExtractLocales())(
            new \SplFileObject(($input->getArgument('output') ?? getcwd()) . '/locales.yml', 'x'),
            $locales
        );

        $style->writeln('locales <fg=green>ok</>', SymfonyStyle::OUTPUT_PLAIN);

        return 0;
    }
}