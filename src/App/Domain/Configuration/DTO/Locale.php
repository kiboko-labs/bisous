<?php

namespace App\Domain\Configuration\DTO;

class Locale
{
    /** @var string */
    public $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }
}