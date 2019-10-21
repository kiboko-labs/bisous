<?php

namespace App\Infrastructure\Command;

class CommandBus
{
    /** @var CommandInterface[]|\SplQueue */
    private $commands;

    public function __construct(CommandInterface ...$commands)
    {
        $this->commands = new \SplQueue();
        $this->add(...$commands);
    }

    public function add(CommandInterface ...$commands)
    {
        foreach ($commands as $command) {
            $this->commands->enqueue($command);
        }
    }

    public function __invoke(\PDO $connection): void
    {
        foreach ($this->commands as $command) {
            $command($connection);
        }
    }
}