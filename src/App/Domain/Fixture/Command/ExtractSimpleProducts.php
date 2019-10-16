<?php

namespace App\Domain\Fixture\Command;

use App\Domain\Fixture\SqlToCsv;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\FamilyVariant;
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

class ExtractSimpleProducts implements LoggerAwareInterface
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
     * @param FamilyVariant[] $familyVariants
     */
    public function __invoke(\SplFileObject $output, array $attributes, array $familyVariants): void
    {
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
                        'mapping' => [],
                        'attributes' => $attributes,
                    ],
                    $this->logger
                ),
                new TwigCommand(
                    $this->twig,
                    'product/02-prefill-axis-attributes.sql.twig',
                    [
                        'mapping' => [],
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

                $bus->add(
                    new TwigCommand(
                        $this->twig,
                        $attribute->template(),
                        [
                            'renderer' => $attribute,
                        ],
                        $this->logger
                    )
                );
            }

            $bus->add(
                new TwigCommand(
                    $this->twig,
                    'product/03-generate-intermediate-product-models.sql.twig',
                    [
                        'variants' => $familyVariants,
                    ],
                    $this->logger
                )
            );

            $bus($this->pdo);

            $view = $this->twig->load('extract-products.sql.twig');

            (new SqlToCsv($this->pdo, $this->logger))
            (
                $view->render([
                    'attributes' => $attributes,
                    'variants' => $familyVariants,
                ]),
                $output
            );
        } catch (\RuntimeException|LoaderError|RuntimeError|SyntaxError|FatalThrowableError $e) {
            throw new \RuntimeException('An error occurred during the product data extraction.', null, $e);
        }
    }
}