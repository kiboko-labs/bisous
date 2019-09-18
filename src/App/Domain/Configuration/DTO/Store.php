<?php

namespace App\Domain\Configuration\DTO;

class Store
{
    /** @var string */
    public $code;
    /** @var Label */
    public $name;
    /** @var int */
    public $storeId;
    /** @var Locale */
    public $locale;

    public function __construct(string $code, Label $name, int $storeId, Locale $locale)
    {
        $this->code = $code;
        $this->name = $name;
        $this->storeId = $storeId;
        $this->locale = $locale;
    }
}