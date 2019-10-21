<?php

namespace App\Domain\Fixture\Command;

use App\Domain\Fixture\SqlToCsv;
use App\Infrastructure\Command\CommandBus;
use App\Infrastructure\Command\TwigCommand;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ExtractAttributeOptions
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

    public function __invoke(
        \SplFileObject $output,
        array $attributes,
        array $locales,
        array $mapping
    ): void {
        try {
            $view = $this->twig->load('extract-attribute-options.sql.twig');
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            throw new \RuntimeException(null, null, $e);
        }

        (new SqlToCsv($this->pdo, $this->logger))
        (
            $view->render([
                'attributes' => $attributes,
                'locales' => $locales,
                'mapping' => $mapping,
            ]),
            $output
        );
    }
}