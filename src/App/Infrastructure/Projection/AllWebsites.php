<?php

namespace App\Infrastructure\Projection;

use App\Domain\Configuration\DTO\Label;
use App\Domain\Configuration\DTO\Website;

class AllWebsites implements \IteratorAggregate
{
    /** @var \PDO */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getIterator()
    {
        $statement = $this->connection->query(<<<SQL
SELECT 
    website.website_id AS id, 
    website.code AS code,
    website.name AS label
FROM core_website AS website
WHERE website.website_id != 0
SQL
        );

        foreach ($statement as $row) {
            yield new Website(
                $row['code'],
                new Label($row['label']),
                ...(new AllStoresFromWebsite($this->connection, $row['id']))
            );
        }
    }
}