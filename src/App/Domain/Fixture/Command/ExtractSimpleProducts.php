<?php

namespace App\Domain\Fixture\Command;

use App\Domain\Fixture\SqlToCsv;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ExtractSimpleProducts
{
    /** @var \PDO */
    private $pdo;
    /** @var Environment */
    private $twig;

    public function __construct(\PDO $pdo, Environment $twig)
    {
        $this->pdo = $pdo;
        $this->twig = $twig;
    }

    public function __invoke(\SplFileObject $output, array $attributes, array $locales): void
    {
        try {
            $view = $this->twig->load('extract-products.sql.twig');

            $query = $view->render([
                'attributes' => $attributes,
                'locales' => $locales,
            ]);

            $view = $this->twig->load('extract-products.sql.twig');

            (new SqlToCsv($this->pdo))
            (
                $view->render([
                    'attributes' => $attributes,
                    'locales' => $locales,
                ]),
                $output
            );
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            throw new \RuntimeException('An error occurred during the product data extraction.', null, $e);
        }
    }
}