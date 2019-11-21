<?php

namespace App\Domain\Magento\Locale;

use App\Domain\Magento\LocaleStore as LocaleReferenceInterface;
use App\Domain\Magento\MagentoStore;

class LocaleStore implements LocaleReferenceInterface
{
    /** @var string */
    private $code;
    /** @var MagentoStore */
    private $default;

    public function __construct(string $code, MagentoStore $default)
    {
        $this->code = $code;
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

    public function store(): MagentoStore
    {
        return $this->default;
    }
}