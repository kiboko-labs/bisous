<?php

namespace App\Domain\Configuration\DTO;

class Website
{
    /** @var string */
    public $code;
    /** @var Label */
    public $name;
    /** @var Store */
    public $defaultStore;
    /** @var Store[] */
    public $stores;

    public function __construct(string $code, Label $name, Store ...$stores)
    {
        $this->code = $code;
        $this->name = $name;
        $this->defaultStore = array_shift($stores);
        $this->stores = $stores;
    }
}