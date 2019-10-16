<?php

namespace App\Domain\Magento\CodeGenerator;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\CodeGenerator as CodeGeneratorInterface;
use App\Domain\Magento\Scope;

class Scoped implements CodeGeneratorInterface
{
    /** @var Attribute */
    private $attribute;
    /** @var Scope */
    private $scope;

    public function __construct(
        Attribute $attribute,
        Scope $scope
    ) {
        $this->attribute = $attribute;
        $this->scope = $scope;
    }

    public function attribute(): string
    {
        return $this->attribute->code();
    }

    public function table(): string
    {
        return strtr(
            'tmp_{{ attribute }}_{{ scope }}',
            [
                '{{ attribute }}' => $this->attribute->code(),
                '{{ scope }}' => $this->scope->code(),
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
        return strtr(
            'attr_{{ attribute }}_{{ scope }}',
            [
                '{{ scope }}' => $this->scope->code(),
                '{{ attribute }}' => $this->attribute->code,
            ]
        );
    }

    public function column(): string
    {
        return strtr(
            '{{ attribute }}-{{ scope }}',
            [
                '{{ attribute }}' => $this->attribute->code(),
                '{{ scope }}' => $this->scope->code(),
            ]
        );
    }
}