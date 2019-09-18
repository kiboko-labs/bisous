<?php

namespace App\Infrastructure\Console\Command;

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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestCommand extends Command
{
    protected static $defaultName = 'test';

    private $logger;

    public function __construct(string $name = null, LoggerInterface $logger = null)
    {
        parent::__construct($name);
        $this->logger = $logger ?? new NullLogger();
    }

    protected function configure()
    {
        $this->setDescription('Tests the syntax of the configuration file.');

        $this->addArgument(
            'configuration',
            InputArgument::OPTIONAL,
            'Specify the configuration file path, defaults to catalog.yml.',
            getcwd() . '/catalog.yml'
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

        $style->success('File syntax is correct!');
        return 0;
    }
}