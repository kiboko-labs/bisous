<?php

namespace App\Infrastructure\Command;

use App\Domain\Magento\AttributeRenderer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AttributeRendererCommand implements CommandInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var Environment */
    private $twig;
    /** @var AttributeRenderer */
    private $renderer;

    public function __construct(
        Environment $twig,
        AttributeRenderer $renderer,
        LoggerInterface $logger = null
    ) {
        $this->twig = $twig;
        $this->renderer = $renderer;
        $this->logger = $logger ?? new NullLogger();
    }

    public function __invoke(\PDO $connection): void
    {
        try {
            $this->logger->debug('Compiling template {template}.', [
                'template' => $this->renderer->template($this->twig)->getTemplateName()
            ]);

            $view = $this->renderer->template($this->twig);
            $queries = explode(';', ($this->renderer)($view));

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