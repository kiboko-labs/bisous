<?php

namespace App\Domain\Magento\Scope;

use App\Domain\Magento\Locale;
use App\Domain\Magento\MagentoStore;
use App\Domain\Magento\Scope as ScopeInterface;

class Scope implements ScopeInterface
{
    /** @var string */
    private $code;
    /** @var MagentoStore */
    private $default;
    /** @var Locale[] */
    private $locales;

    public function __construct(string $code, MagentoStore $default, Locale ...$locales)
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