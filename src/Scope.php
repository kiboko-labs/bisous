<?php

namespace Kiboko\Bridge\Akeneo\Magento;

interface Scope
{
    /**
     * @return Locale[]
     */
    public function locales(): iterable;
    public function code(): string;
    public function store(): MagentoStore;
}