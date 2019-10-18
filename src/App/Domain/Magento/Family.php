<?php

namespace App\Domain\Magento;

class Family
{
    /** @var string */
    public $code;
    /** @var AttributeRenderer[] */
    public $attributes;

    public function __construct(string $code, AttributeRenderer ...$attributes)
    {
        $this->code = $code;
        $this->attributes = $attributes;
    }
}