<?php

namespace App\Domain\Configuration\DTO;

class Label
{
    /** @var string */
    public $fallback;
    /** @var Localised[] */
    public $localised;

    public function __construct(?string $fallback, Localised ...$localised)
    {
        $this->fallback = $fallback;
        $this->localised = $localised;
    }

    public function localised(Locale $locale): string
    {
        foreach ($this->localised as $localised) {
            if ($localised->locale->code === $locale->code) {
                return $localised->label;
            }
        }

        return $this->fallback;
    }

    public function __toString()
    {
        return $this->fallback ?? self::class;
    }
}