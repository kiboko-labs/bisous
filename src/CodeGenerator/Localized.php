<?php

namespace Kiboko\Bridge\Akeneo\Magento\CodeGenerator;

use Kiboko\Bridge\Akeneo\Magento\Attribute;
use Kiboko\Bridge\Akeneo\Magento\CodeGenerator as CodeGeneratorInterface;
use Kiboko\Bridge\Akeneo\Magento\Locale;

class Localized implements CodeGeneratorInterface
{
    /** @var Attribute */
    private $attribute;
    /** @var Locale */
    private $locale;

    public function __construct(
        Attribute $attribute,
        Locale $locale
    ) {
        $this->attribute = $attribute;
        $this->locale = $locale;
    }

    public function table(): string
    {
        return strtr(
            'tmp_{{ attribute }}_{{ locale }}',
            [
                '{{ attribute }}' => $this->attribute->codeInSource(),
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
            'attr_{{ locale }}',
            [
                '{{ locale }}' => $this->locale->code(),
            ]
        );
    }

    public function column(): string
    {
        return strtr(
            '{{ attribute }}-{{ locale }}',
            [
                '{{ attribute }}' => $this->attribute->codeInSource(),
                '{{ locale }}' => $this->locale->code(),
            ]
        );
    }
}