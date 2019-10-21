<?php

namespace App\Domain\Magento;

class Field
{
    /** @var CodeGenerator */
    public $codeGenerator;
    /** @var int */
    public $store;

    public function __construct(
        CodeGenerator $codeGenerator,
        MagentoStore $store
    ) {
        $this->codeGenerator = $codeGenerator;
        $this->store = $store;
    }

    public function __toString()
    {
        return $this->codeGenerator->column($this);
    }
}