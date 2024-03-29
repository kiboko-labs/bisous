<?php

namespace App\Domain\Magento\CodeGenerator;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\CodeGenerator as CodeGeneratorInterface;
use App\Domain\Magento\LocaleStore;

class Localized implements CodeGeneratorInterface
{
    /** @var Attribute */
    private $attribute;
    /** @var LocaleStore */
    private $locale;

    public function __construct(
        Attribute $attribute,
        LocaleStore $locale
    ) {
        $this->attribute = $attribute;
        $this->locale = $locale;
    }

    public function attribute(): string
    {
        return $this->attribute->code();
    }

    public function table(): string
    {
        return strtr(
            'tmp_{{ attribute }}_{{ locale }}',
            [
                '{{ attribute }}' => $this->attribute->code,
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
            'attr_{{ attribute }}_{{ locale }}',
            [
                '{{ locale }}' => $this->locale->code(),
                '{{ attribute }}' => $this->attribute->code,
            ]
        );
    }

    public function column(): string
    {
        return strtr(
            '{{ attribute }}-{{ locale }}',
            [
                '{{ attribute }}' => $this->attribute->code(),
                '{{ locale }}' => $this->locale->code(),
            ]
        );
    }
}