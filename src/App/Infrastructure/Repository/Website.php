<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\Projection\AllWebsites;

class Website
{
    /** @var \PDO */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function all(): iterable
    {
        return new AllWebsites($this->connection);
    }
}