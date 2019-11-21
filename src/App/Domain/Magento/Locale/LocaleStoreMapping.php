<?php

namespace App\Domain\Magento\Locale;

use App\Domain\Magento\Locale as LocaleInterface;
use App\Domain\Magento\MagentoStore;

class LocaleStoreMapping implements LocaleInterface
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

    public function __toString()
    {
        return (string) $this->locale;
    }

    public function code(): string
    {
        return $this->locale->code();
    }

    public function currency(): string
    {
        return $this->locale->currency();
    }

    public function store(): MagentoStore
    {
        return $this->store;
    }
}