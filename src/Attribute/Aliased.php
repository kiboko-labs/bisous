<?php

namespace Kiboko\Bridge\Akeneo\Magento\Attribute;

use Kiboko\Bridge\Akeneo\Magento\Attribute;

class Aliased implements Attribute
{
    /** @var string */
    public $code;
    /** @var string */
    public $alias;

    public function __construct(string $code, string $alias)
    {
        $this->code = $code;
        $this->alias = $alias;
    }

    public function codeInSource(): string
    {
        return $this->code;
    }

    public function codeInDestination(): string
    {
        return $this->alias;
    }
}