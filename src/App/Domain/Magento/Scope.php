<?php

namespace App\Domain\Magento;

interface Scope
{
    /**
     * @return Locale[]
     */
    public function locales(): iterable;
    public function code(): string;
    public function store(): MagentoStore;
}