<?php

namespace App\Domain\Magento;

class FamilyVariantAxis
{
    /** @var AttributeRenderer[] */
    public $attributes;

    public function __construct(AttributeRenderer ...$renderers)
    {
        $this->attributes = $renderers;
    }
}