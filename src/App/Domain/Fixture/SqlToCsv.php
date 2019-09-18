<?php

namespace App\Domain\Fixture;

class SqlToCsv
{
    /** @var \PDO */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function __invoke(string $sql, \SplFileObject $output): self
    {
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