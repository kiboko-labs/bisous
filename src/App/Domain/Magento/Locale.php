<?php

namespace App\Domain\Magento;

interface Locale extends LocaleStore
{
    public function currency(): string;
    public function store(): MagentoStore;
}