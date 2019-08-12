<?php

namespace Kiboko\Bridge\Akeneo\Magento;

interface Locale
{
    public function code(): string;
    public function store(): MagentoStore;
}