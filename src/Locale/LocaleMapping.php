<?php

namespace Kiboko\Bridge\Akeneo\Magento\Locale;

use Kiboko\Bridge\Akeneo\Magento\Locale as LocaleInterface;
use Kiboko\Bridge\Akeneo\Magento\MagentoStore;

class LocaleMapping implements LocaleInterface
{
    /** @var Locale */
    private $locale;
    /** @var MagentoStore */
    private $store;

    public function __construct(Locale $locale, MagentoStore $store)
    {
        $this->locale = $locale;
        $this->store = $store;
    }

    public function code(): string
    {
        return $this->locale->code();
    }

    public function store(): MagentoStore
    {
        return $this->store;
    }
}