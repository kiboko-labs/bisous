<?php

namespace App\Domain\Configuration\DTO;

class Family
{
    /** @var string */
    public $code;
    /** @var Label */
    public $label;
    /** @var AttributeGroup[] */
    public $attributeGroups;

    public function __construct(string $code, Label $label, AttributeGroup ...$attributeGroups)
    {
        $this->code = $code;
        $this->label = $label;
        $this->attributeGroups = $attributeGroups;
    }
}