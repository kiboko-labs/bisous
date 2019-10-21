<?php

namespace App\Infrastructure\Command;

interface CommandInterface
{
    public function __invoke(\PDO $connection): void;
}