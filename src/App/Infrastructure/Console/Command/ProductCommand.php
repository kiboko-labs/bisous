<?php

namespace App\Infrastructure\Console\Command;

use App\Domain\Fixture\IterableToCsv;
use App\Infrastructure\Configuration\YamlFileLoader;
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

class ProductCommand extends Command
{
    protected static $defaultName = 'products';

    protected function configure()
    {
        $this->setDescription('Export families list.');

        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Specify the path to the catalog config file.'
        );

        $this->addArgument(
            'output',
            InputArgument::OPTIONAL,
            'Specify the output file, defaults to STDOUT.',
            'php://stdout'
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

        $output = new \SplFileObject($input->getArgument('output') ?? 'php://stdout', 'w');

        $columns = array_merge(
            [
                'code',
            ],
            array_map(function($locale) {
                return 'label-' . $locale;
            }, $config['locales']),
            [
                'attributes',
                'attribute_as_image',
                'attribute_as_label',
            ],
            array_map(function($scope) {
                return 'requirements-' . $scope;
            }, array_keys($config['scopes']))
        );

        (new IterableToCsv($columns))
            (
                (function(array $config) {
                    foreach ($config['families'] as $code => $family) {
                        yield array_merge(
                            [
                                'code' => $code,
                                'attributes' => implode(',', $family['attributes']),
                                'attribute_as_label' => $family['label'],
                                'attribute_as_image' => $family['image'],
                            ],
                            ...array_map(function($requirements) {
                                return [
                                    'requirements-' . $requirements['scope'] => implode(',', $requirements['attributes'])
                                ];
                            }, $family['requirements']),
                            ...array_map(function($locale) use($code) {
                                return [
                                    'label-' . $locale => sprintf('%s (%s)', $code, $locale),
                                ];
                            }, $config['locales'])
                        );
                    }
                })($config),
                $output
            );

        return 0;
    }
}