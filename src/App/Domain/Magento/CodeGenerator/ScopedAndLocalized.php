<?php

namespace App\Domain\Magento\CodeGenerator;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\CodeGenerator as CodeGeneratorInterface;
use App\Domain\Magento\LocaleStore;
use App\Domain\Magento\Scope;

class ScopedAndLocalized implements CodeGeneratorInterface
{
    /** @var Attribute */
    private $attribute;
    /** @var Scope */
    private $scope;
    /** @var LocaleStore */
    private $locale;

    public function __construct(
        Attribute $attribute,
        Scope $scope,
        LocaleStore $locale
    ) {
        $this->attribute = $attribute;
        $this->scope = $scope;
        $this->locale = $locale;
    }

    public function attribute(): string
    {
        return $this->attribute->code();
    }

    public function table(): string
    {
        return strtr(
            'tmp_{{ attribute }}_{{ scope }}_{{ locale }}',
            [
                '{{ attribute }}' => $this->attribute->code(),
                '{{ scope }}' => $this->scope->code(),
                '{{ locale }}' => $this->locale->code(),
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
            'attr_{{ attribute }}_{{ scope }}_{{ locale }}',
            [
                '{{ scope }}' => $this->scope->code(),
                '{{ locale }}' => $this->locale->code(),
                '{{ attribute }}' => $this->attribute->code,
            ]
        );
    }

    public function column(): string
    {
        return strtr(
            '{{ attribute }}-{{ locale }}-{{ scope }}',
            [
                '{{ attribute }}' => $this->attribute->code(),
                '{{ scope }}' => $this->scope->code(),
                '{{ locale }}' => $this->locale->code(),
            ]
        );
    }
}