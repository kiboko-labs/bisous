<?php

namespace App\Domain\Magento\Scope;

use App\Domain\Magento\LocaleStore;
use App\Domain\Magento\MagentoStore;
use App\Domain\Magento\Scope as ScopeInterface;

class Scope implements ScopeInterface
{
    /** @var string */
    private $code;
    /** @var MagentoStore */
    private $default;
    /** @var LocaleStore[] */
    private $locales;

    public function __construct(string $code, MagentoStore $default, LocaleStore ...$locales)
    {
        $this->code = $code;
        $this->default = $default;
        $this->locales = $locales;
    }

    public function __toString()
    {
        return $this->code;
    }

    public function locales(): iterable
    {
        return $this->locales;
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