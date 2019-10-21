<?php

namespace App\Domain\Magento\Attribute;

use App\Domain\Magento\Attribute;

class AdHoc implements Attribute
{
    /** @var string */
    public $code;
    /** @var string */
    public $group;

    public function __construct(string $code, string $group)
    {
        $this->code = $code;
        $this->group = $group;
    }

    public function __toString()
    {
        return 'ad-hoc';
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