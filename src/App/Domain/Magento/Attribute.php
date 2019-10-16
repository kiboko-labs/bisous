<?php

namespace App\Domain\Magento;

interface Attribute
{
    public function code(): string;
    public function source(): string;
}