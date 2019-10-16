<?php

namespace App\Domain\Fixture;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class SqlToCsv
{
    use LoggerAwareTrait;

    /** @var \PDO */
    private $connection;

    public function __construct(\PDO $connection, ?LoggerInterface $logger = null)
    {
        $this->connection = $connection;
        $this->logger = $logger ?? new NullLogger();
    }

    public function __invoke(string $sql, \SplFileObject $output): self
    {
        $this->logger->debug($sql);
        $statement = $this->connection->query($sql);

        $columnCount = $statement->columnCount();
        $columns = [];
        for ($i = 0; $i < $columnCount; ++$i) {
            $columns[] = $statement->getColumnMeta($i)['name'];
        }
        $output->fputcsv($columns, ';');
        foreach ($statement as $item) {
            $output->fputcsv($item, ';');
        }

        return $this;
    }
}