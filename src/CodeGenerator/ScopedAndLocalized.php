<?php

namespace Kiboko\Bridge\Akeneo\Magento\CodeGenerator;

use Kiboko\Bridge\Akeneo\Magento\Attribute;
use Kiboko\Bridge\Akeneo\Magento\CodeGenerator as CodeGeneratorInterface;
use Kiboko\Bridge\Akeneo\Magento\Locale;
use Kiboko\Bridge\Akeneo\Magento\Scope;

class ScopedAndLocalized implements CodeGeneratorInterface
{
    /** @var Attribute */
    private $attribute;
    /** @var Scope */
    private $scope;
    /** @var Locale */
    private $locale;

    public function __construct(
        Attribute $attribute,
        Scope $scope,
        Locale $locale
    ) {
        $this->attribute = $attribute;
        $this->scope = $scope;
        $this->locale = $locale;
    }

    public function table(): string
    {
        return strtr(
            'tmp_{{ attribute }}_{{ scope }}_{{ locale }}',
            [
                '{{ attribute }}' => $this->attribute->codeInSource(),
                '{{ scope }}' => $this->scope->code(),
                '{{ locale }}' => $this->locale->code(),
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
            'attr_{{ scope }}_{{ locale }}',
            [
                '{{ scope }}' => $this->scope->code(),
                '{{ locale }}' => $this->locale->code(),
            ]
        );
    }

    public function column(): string
    {
        return strtr(
            '{{ attribute }}-{{ locale }}-{{ scope }}',
            [
                '{{ attribute }}' => $this->attribute->codeInSource(),
                '{{ scope }}' => $this->scope->code(),
                '{{ locale }}' => $this->locale->code(),
            ]
        );
    }
}