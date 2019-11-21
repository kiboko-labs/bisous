<?php

namespace App\Domain\Magento;

interface LocaleStore
{
    public function code(): string;
    public function __toString();
}