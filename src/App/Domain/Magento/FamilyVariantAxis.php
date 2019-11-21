<?php

namespace App\Domain\Magento;

class FamilyVariantAxis
{
    /** @var AttributeRenderer[] */
    public $attributes;
    /** @var AxisAttributeList */
    public $axises;

    public function __construct(AxisAttributeList $axises, AttributeRenderer ...$attributes)
    {
        $this->axises = $axises;
        $this->attributes = $attributes;
    }

    public function attributes(): \Traversable
    {
        return new \ArrayIterator($this->attributes);
    }

    public function axises(): \Traversable
    {
        return $this->axises->getIterator();
    }
}