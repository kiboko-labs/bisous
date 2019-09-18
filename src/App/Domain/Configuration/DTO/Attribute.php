<?php

namespace App\Domain\Configuration\DTO;

class Attribute
{
    /** @var string */
    public $code;
    /** @var Label */
    public $label;
    /** @var string */
    public $strategy;
    /** @var string */
    public $type;

    public function __construct(
        string $code,
        Label $label,
        string $strategy,
        string $type
    ) {
        $this->code = $code;
        $this->label = $label;
        $this->strategy = $strategy;
        $this->type = $type;
    }
}