<?php

namespace App\Domain\Magento;

class MagentoStore
{
    /** @var int */
    public $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}