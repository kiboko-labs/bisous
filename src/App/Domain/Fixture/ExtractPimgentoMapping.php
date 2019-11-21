<?php

namespace App\Domain\Fixture;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ExtractPimgentoMapping
{
    use LoggerAwareTrait;

    /** @var \PDO */
    private $connection;

    public function __construct(\PDO $connection, ?LoggerInterface $logger = null)
    {
        $this->connection = $connection;
        $this->logger = $logger ?? new NullLogger();
    }

    public function __invoke(string $sql, callable $mapper, \SplFileObject $output): self
    {
        $this->logger->debug($sql);
        $statement = $this->connection->query($sql);

        foreach ($statement as $item) {
            $output->fwrite($this->format(...$mapper($item)));
        }

        return $this;
    }

    private function format(string $import, string $code, int $id)
    {
        return strtr(
            'INSERT INTO pimgento_entities (import, code, entity_id) VALUES ("%import%", "%code%", %id%);',
            [
                '%import%' => $import,
                '%code%' => $code,
                '%id%' => number_format($id, 0, '', ''),
            ]
        ) . PHP_EOL;
    }
}