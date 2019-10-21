<?php

namespace App\Domain\Configuration\DTO;

class Localised
{
    /** @var Locale */
    public $locale;
    /** @var string */
    public $label;

    public function __construct(Locale $locale, string $label)
    {
        $this->locale = $locale;
        $this->label = $label;
    }
}