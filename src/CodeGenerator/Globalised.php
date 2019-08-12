<?php

namespace Kiboko\Bridge\Akeneo\Magento\CodeGenerator;

use Kiboko\Bridge\Akeneo\Magento\Attribute;
use Kiboko\Bridge\Akeneo\Magento\CodeGenerator as CodeGeneratorInterface;

class Globalised implements CodeGeneratorInterface
{
    /** @var Attribute */
    private $attribute;

    public function __construct(
        Attribute $attribute
    ) {
        $this->attribute = $attribute;
    }

    public function table(): string
    {
        return strtr(
            'tmp_{{ attribute }}',
            [
                '{{ attribute }}' => $this->attribute->codeInSource(),
            ]
        );
    }

    public function default(): string
    {
        return 'attr_default';
    }

    public function alias(): string
    {
        return $this->default();
    }

    public function column(): string
    {
        return $this->attribute->codeInDestination();
    }
}