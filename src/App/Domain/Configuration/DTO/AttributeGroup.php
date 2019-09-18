<?php

namespace App\Domain\Configuration\DTO;

class AttributeGroup
{
    /** @var string */
    public $code;
    /** @var Label */
    public $label;
    /** @var Attribute[] */
    public $attributes;

    public function __construct(string $code, Label $label, Attribute ...$attributes)
    {
        $this->code = $code;
        $this->label = $label;
        $this->attributes = $attributes;
    }
}