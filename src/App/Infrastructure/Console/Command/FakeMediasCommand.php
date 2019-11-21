<?php

namespace App\Infrastructure\Console\Command;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\Locale;
use App\Domain\Magento\Scope;
use App\Infrastructure\AttributeRendererFactory;
use App\Infrastructure\Configuration\YamlFileLoader;
use App\Infrastructure\Console\MinimalGif;
use App\Infrastructure\Console\MinimalJpeg;
use App\Infrastructure\Console\MinimalPng;
use App\Infrastructure\Console\FakeGif;
use App\Infrastructure\Console\FakeJpeg;
use App\Infrastructure\Console\FakePng;
use App\Infrastructure\Normalizer\Magento\AttributeDenormalizerFactory;
use App\Infrastructure\Normalizer\Magento\LocaleDenormalizerFactory;
use App\Infrastructure\Normalizer\Magento\ScopeDenormalizerFactory;
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

class FakeMediasCommand extends Command
{
    protected static $defaultName = 'fake-medias';

    private $logger;

    public function __construct(string $name = null, LoggerInterface $logger = null)
    {
        parent::__construct($name);
        $this->logger = $logger ?? new NullLogger();
    }

    protected function configure()
    {
        $this->setDescription('Generate fake or minimal image files from the products.csv and product_models.csv files.');

        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Specify the path to the catalog config file.'
        );

        $this->addOption(
            'fake',
            null,
            InputOption::VALUE_OPTIONAL,
            'Prefer fake images over minimal images.'
        );

        $this->addArgument(
            'path',
            InputArgument::OPTIONAL,
            'Specify the path of products.csv and product_models.csv files, also where the image files will be created, defaults to CWD.',
            getcwd()
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

        $attributeRenderers = array_filter($attributeRenderers, function(AttributeRenderer $attributeRenderer) {
            return $attributeRenderer instanceof AttributeRenderer\Image
                || $attributeRenderer instanceof AttributeRenderer\ImageGalleryItem;
        });

        $csvLocator = new FileLocator([$input->getArgument('path')]);

        $basePath = realpath($input->getArgument('path'));

        $file = new \SplFileObject($csvLocator->locate('products.csv'), 'r');
        $file->setCsvControl(';', '"', '"');

        $this->walkFile($basePath, $file, $attributeRenderers);

        $file = new \SplFileObject($csvLocator->locate('product_models.csv'), 'r');
        $file->setCsvControl(';', '"', '"');

        $this->walkFile($basePath, $file, $attributeRenderers, $input->getOption('fake'));

        return 0;
    }

    private function walkFile(string $basePath, \SplFileObject $file, array $attributeRenderers, bool $isFake = false): void
    {
        $columns = $file->fgetcsv();

        $columnIds = [];
        foreach ($attributeRenderers as $attributeRenderer) {
            $columnId = array_search($attributeRenderer->attribute()->code(), $columns, true);

            if ($columnId === false) {
                continue;
            }

            $columnIds[] = $columnId;
        }

        if (count($columnIds) <= 0) {
            return;
        }

        $jpeg = $isFake === false ? new MinimalJpeg() : new FakeJpeg();
        $gif = $isFake === false ? new MinimalGif() : new FakeGif();
        $png = $isFake === false ? new MinimalPng() : new FakePng();

        while (!$file->eof()) {
            $line = $file->fgetcsv();

            foreach ($columnIds as $columnId) {
                if (!isset($line[$columnId]) || empty($line[$columnId])) {
                    continue;
                }
                if (file_exists($basePath . '/' . $line[$columnId])) {
                    continue;
                }

                $directory = dirname($basePath . '/' . $line[$columnId]);
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $this->logger->info(strtr('Writing file %file%.', [
                    '%file%' => $basePath . '/' . $line[$columnId],
                ]));

                if (preg_match('/\.jpe?g$/', $line[$columnId])) {
                    file_put_contents($basePath . '/' . $line[$columnId], $jpeg);
                } else if (preg_match('/\.gif$/', $line[$columnId])) {
                    file_put_contents($basePath . '/' . $line[$columnId], $gif);
                } else if (preg_match('/\.png/', $line[$columnId])) {
                    file_put_contents($basePath . '/' . $line[$columnId], $png);
                } else {
                    touch($basePath . '/' . $line[$columnId]);
                }
            }
        }
    }
}