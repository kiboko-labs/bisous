<?php

namespace Kiboko\Bridge\Akeneo\Magento\Attribute;

use Kiboko\Bridge\Akeneo\Magento\Attribute;

class AdHoc implements Attribute
{
    /** @var string */
    public $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function codeInSource(): string
    {
        return $this->code;
    }

    public function codeInDestination(): string
    {
        return $this->code;
    }
}