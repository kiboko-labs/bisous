<?php

namespace App\Domain\Magento\Attribute;

use App\Domain\Magento\Attribute;

class Aliased implements Attribute
{
    /** @var string */
    public $code;
    /** @var string */
    public $source;

    public function __construct(string $code, string $source)
    {
        $this->code = $code;
        $this->source = $source;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function source(): string
    {
        return $this->source;
    }
}