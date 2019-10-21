<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\Projection\AllStores;
use App\Infrastructure\Projection\AllStoresFromWebsite;

class Store
{
    /** @var \PDO */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function all(): iterable
    {
        return new AllStores($this->connection);
    }

    public function fromWebsite(int $websiteId): iterable
    {
        return new AllStoresFromWebsite($this->connection, $websiteId);
    }
}