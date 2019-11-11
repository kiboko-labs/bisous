<?php

namespace App\Domain\Fixture\Command;

use App\Domain\Fixture\SqlToCsv;
use App\Domain\Magento\Attribute\ExNihilo;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\Family;
use App\Domain\Magento\FamilyVariant;
use App\Infrastructure\AttributeAggregator;
use App\Infrastructure\Command\AttributeRendererCommand;
use App\Infrastructure\FamilyVariantAxisAttributeAggregator;
use App\Infrastructure\Command\CommandBus;
use App\Infrastructure\Command\TwigCommand;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ExtractProducts implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \PDO */
    private $pdo;
    /** @var Environment */
    private $twig;

    public function __construct(\PDO $pdo, Environment $twig, ?LoggerInterface $logger = null)
    {
        $this->pdo = $pdo;
        $this->twig = $twig;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param AttributeRenderer[] $attributes
     * @param Family[] $families
     * @param FamilyVariant[] $familyVariants
     */
    public function __invoke(
        \SplFileObject $productOutput,
        \SplFileObject $productModelOutput,
        array $attributes,
        array $families,
        array $familyVariants,
        array $mapping
    ): void {
        $bus = new CommandBus();
        try {
            $bus->add(
                new TwigCommand(
                    $this->twig,
                    'product/00-sku.sql.twig',
                    [],
                    $this->logger
                ),
                new TwigCommand(
                    $this->twig,
                    'product/01-attribute-options.sql.twig',
                    [
                        'mapping' => $mapping,
                        'attributes' => array_merge(
                            (new AttributeAggregator())(...$families),
                            (new FamilyVariantAxisAttributeAggregator())(...$familyVariants)
                        ),
                    ],
                    $this->logger
                ),
                new TwigCommand(
                    $this->twig,
                    'product/02-prefill-axis-attributes.sql.twig',
                    [
                        'mapping' => $mapping,
                        'variants' => $familyVariants,
                    ],
                    $this->logger
                )
            );

            foreach ($attributes as $attribute) {
                if (!$attribute instanceof AttributeRenderer) {
                    throw new \RuntimeException(strtr(
                        'Expected an instance of %expected%, but got %actual%.',
                        [
                            '%expected%' => AttributeRenderer::class,
                            '%actual%' => is_object($attribute) ? get_class($attribute) : gettype($attribute),
                        ]
                    ));
                }

                if ($attribute->attribute() instanceof ExNihilo) {
                    continue;
                }

                try {
                    $bus->add(
                        new AttributeRendererCommand(
                            $this->twig,
                            $attribute,
                            $this->logger
                        )
                    );
                } catch (\RuntimeException $e) {
                    continue;
                }
            }

            $bus->add(
                new TwigCommand(
                    $this->twig,
                    'product/03-generate-intermediate-product-models.sql.twig',
                    [
                        'variants' => $familyVariants,
                        'attributes' => (new FamilyVariantAxisAttributeAggregator())(...$familyVariants),
                    ],
                    $this->logger
                )
            );

            $bus->add(
                new TwigCommand(
                    $this->twig,
                    'product/04-consolidate-product-models.sql.twig',
                    [
                        'attributes' => (new FamilyVariantAxisAttributeAggregator())(...$familyVariants),
                    ],
                    $this->logger
                )
            );

            $bus($this->pdo);

            $view = $this->twig->load('extract-products.sql.twig');

            (new SqlToCsv($this->pdo, $this->logger))
            (
                $view->render([
                    'attributes' => (new AttributeAggregator())(...$families),
                    'variants' => $familyVariants,
                ]),
                $productOutput
            );

            $view = $this->twig->load('extract-product-models.sql.twig');

            (new SqlToCsv($this->pdo, $this->logger))
            (
                $view->render([
                    'attributes' => (new FamilyVariantAxisAttributeAggregator())(...$familyVariants),
                    'variants' => $familyVariants,
                ]),
                $productModelOutput
            );
        } catch (\RuntimeException|LoaderError|RuntimeError|SyntaxError|FatalThrowableError $e) {
            throw new \RuntimeException('An error occurred during the product data extraction.', null, $e);
        }
    }
}