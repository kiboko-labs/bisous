<?php

namespace App\Domain\Magento\Attribute;

use App\Domain\Magento\Attribute;

class Aliased implements Attribute
{
    /** @var string */
    public $code;
    /** @var string */
    public $source;
    /** @var string */
    public $group;

    public function __construct(string $code, string $source, string $group)
    {
        $this->code = $code;
        $this->source = $source;
        $this->group = $group;
    }

    public function __toString()
    {
        return 'aliased';
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