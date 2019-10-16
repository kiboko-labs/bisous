<?php

namespace App\Domain\Magento\Locale;

use App\Domain\Magento\Locale as LocaleInterface;
use App\Domain\Magento\MagentoStore;

class Locale implements LocaleInterface
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

    public function code(): string
    {
        return $this->code;
    }

    public function store(): MagentoStore
    {
        return $this->default;
    }
}