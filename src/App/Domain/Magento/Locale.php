<?php

namespace App\Domain\Magento;

interface Locale
{
    public function code(): string;
    public function store(): MagentoStore;
}