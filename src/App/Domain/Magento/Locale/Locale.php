<?php

namespace App\Domain\Magento\Locale;

use App\Domain\Magento\Locale as LocaleInterface;
use App\Domain\Magento\MagentoStore;

class Locale implements LocaleInterface
{
    /** @var string */
    private $code;
    /** @var string */
    private $currency;
    /** @var MagentoStore */
    private $default;

    public function __construct(string $code, string $currency, MagentoStore $default)
    {
        $this->code = $code;
        $this->currency = $currency;
        $this->default = $default;
    }

    public function __toString()
    {
        return $this->code;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function store(): MagentoStore
    {
        return $this->default;
    }
}