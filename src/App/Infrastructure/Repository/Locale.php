<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\Projection\OneLocaleFromStore;

class Locale
{
    /** @var \PDO */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function fromStore(int $websiteId, int $storeId): iterable
    {
        return new OneLocaleFromStore($this->connection, $websiteId, $storeId);
    }
}