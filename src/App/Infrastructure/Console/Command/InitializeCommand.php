<?php

namespace App\Infrastructure\Console\Command;

use App\Domain\Configuration\DTO\Attribute;
use App\Domain\Configuration\DTO\AttributeGroup;
use App\Domain\Configuration\DTO\Family;
use App\Domain\Configuration\DTO\Store;
use App\Domain\Configuration\DTO\Website;
use App\Infrastructure\Configuration\Configuration;
use App\Infrastructure\Normalizer\ListNormalizer;
use App\Infrastructure\Normalizer\Yaml\AttributeNormalizer;
use App\Infrastructure\Normalizer\Yaml\LabelNormalizer;
use App\Infrastructure\Iteration\MapIterator;
use App\Infrastructure\Projection\AllAttributes;
use App\Infrastructure\Projection\AllFamilies;
use App\Infrastructure\Projection\AllWebsites;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class InitializeCommand extends Command
{
    protected static $defaultName = 'init';

    private $logger;

    public function __construct(string $name = null, LoggerInterface $logger = null)
    {
        parent::__construct($name);
        $this->logger = $logger ?? new NullLogger();
    }

    protected function configure()
    {
        $this->setDescription('Creates an initial configuration file from Magento configuration.');

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

        $this->addArgument(
            'output',
            InputArgument::OPTIONAL,
            'Specify the output file, defaults to STDOUT.',
            'php://stdout'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

        $websites = new AllWebsites($pdo);
        $families = new AllFamilies($pdo);
        $attributes = new AllAttributes($pdo);

        $labelNormalizer = new LabelNormalizer();
        $attributeNormalizer = new AttributeNormalizer($labelNormalizer, $labelNormalizer);
        $attributesNormalizer = new ListNormalizer($attributeNormalizer, $attributeNormalizer);

        $config = [
            'catalog' => [
                'locales' => array_values(array_unique(array_merge(
                    [],
                    ...new MapIterator(function(Website $website) {
                        return array_map(function(Store $store) {
                            return $store->locale->code;
                        }, $website->stores);
                    }, new \CallbackFilterIterator(new \IteratorIterator($websites), function (Website $website) {
                        return count($website->stores) > 0;
                    }))
                ))),
                'scopes' => iterator_to_array(new MapIterator(function(Website $website) {
                    return [
                        'code' => $website->code,
                        'locales' => array_map(function(Store $store) {
                            return [
                                'code' => $store->locale->code,
                                'store' => $store->storeId,
                            ];
                        }, $website->stores),
                    ];
                }, new \CallbackFilterIterator(new \IteratorIterator($websites), function (Website $website) {
                    return count($website->stores) > 0;
                }))),
                'families' => iterator_to_array(new MapIterator(function(Family $family) {
                    return [
                        'code' => $family->code,
                        'attributes' => array_merge([], ...array_values(array_map(function(AttributeGroup $group) {
                            return array_values(array_map(function(Attribute $attribute) {
                                return $attribute->code;
                            }, $group->attributes));
                        }, $family->attributeGroups))),
                        'variations' => [],
                    ];
                }, $families)),
                'attributes' => $attributesNormalizer->normalize(
                    new AllAttributes($pdo),
                    'yaml'
                ),
            ]
        ];

        $processor = new Processor();

        $processor->processConfiguration(
            new Configuration(),
            $config
        );

        $output->write(Yaml::dump($config, 4, 2));
    }
}