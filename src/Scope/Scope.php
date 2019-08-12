<?php

namespace Kiboko\Bridge\Akeneo\Magento\Scope;

use Kiboko\Bridge\Akeneo\Magento\Locale;
use Kiboko\Bridge\Akeneo\Magento\MagentoStore;
use Kiboko\Bridge\Akeneo\Magento\Scope as ScopeInterface;

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