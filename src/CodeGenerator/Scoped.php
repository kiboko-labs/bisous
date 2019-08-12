<?php

namespace Kiboko\Bridge\Akeneo\Magento\CodeGenerator;

use Kiboko\Bridge\Akeneo\Magento\Attribute;
use Kiboko\Bridge\Akeneo\Magento\CodeGenerator as CodeGeneratorInterface;
use Kiboko\Bridge\Akeneo\Magento\Locale;
use Kiboko\Bridge\Akeneo\Magento\Scope;

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

    public function table(): string
    {
        return strtr(
            'tmp_{{ attribute }}_{{ scope }}',
            [
                '{{ attribute }}' => $this->attribute->codeInSource(),
                '{{ scope }}' => $this->scope->code(),
            ]
        );
    }

    public function default(): string
    {
        return 'attr_default';
    }

    public function alias(): string
    {
        return strtr(
            'attr_{{ scope }}',
            [
                '{{ scope }}' => $this->scope->code(),
            ]
        );
    }

    public function column(): string
    {
        return strtr(
            '{{ attribute }}-{{ scope }}',
            [
                '{{ attribute }}' => $this->attribute->codeInSource(),
                '{{ scope }}' => $this->scope->code(),
            ]
        );
    }
}