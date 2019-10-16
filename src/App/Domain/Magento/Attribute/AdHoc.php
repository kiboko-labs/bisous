<?php

namespace App\Domain\Magento\Attribute;

use App\Domain\Magento\Attribute;

class AdHoc implements Attribute
{
    /** @var string */
    public $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function source(): string
    {
        return $this->code;
    }
}