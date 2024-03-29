<?php

namespace App\Infrastructure\Command;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TwigCommand implements CommandInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var Environment */
    private $twig;
    /** @var string */
    private $twigTemplate;
    /** @var array */
    private $twigContext;

    public function __construct(
        Environment $twig,
        string $twigTemplate,
        array $twigContext = [],
        LoggerInterface $logger = null
    ) {
        $this->twig = $twig;
        $this->twigTemplate = $twigTemplate;
        $this->twigContext = $twigContext;
        $this->logger = $logger ?? new NullLogger();
    }

    public function __invoke(\PDO $connection): void
    {
        try {
            $this->logger->info('Compiling template {template}.', [
                'template' => $this->twigTemplate
            ]);

            $view = $this->twig->load($this->twigTemplate);
            $queries = explode(';', $view->render($this->twigContext));

            foreach ($queries as $query) {
                $query = trim($query);
                if (empty($query)) {
                    continue;
                }
                $this->logger->debug($query);
                $connection->exec($query);
            }
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            throw new \RuntimeException(
                'An error occurred during the data preparation SQL rendering: '
                . ($query ?? 'query was not generated'), null, $e);
        }
    }
}