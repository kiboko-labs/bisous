<?php

namespace App\Domain\Magento\CodeGenerator;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\CodeGenerator as CodeGeneratorInterface;

class Globalised implements CodeGeneratorInterface
{
    /** @var Attribute */
    private $attribute;

    public function __construct(
        Attribute $attribute
    ) {
        $this->attribute = $attribute;
    }

    public function attribute(): string
    {
        return $this->attribute->code();
    }

    public function table(): string
    {
        return strtr(
            'tmp_{{ attribute }}',
            [
                '{{ attribute }}' => $this->attribute->code,
            ]
        );
    }

    public function default(): string
    {
        return strtr(
            'attr_default_{{ attribute }}',
            [
                '{{ attribute }}' => $this->attribute->code,
            ]
        );
    }

    public function alias(): string
    {
        return $this->default();
    }

    public function column(): string
    {
        return $this->attribute->code();
    }
}