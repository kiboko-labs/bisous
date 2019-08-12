<?php

namespace Kiboko\Bridge\Akeneo\Magento\Scope;

use Kiboko\Bridge\Akeneo\Magento\MagentoStore;
use Kiboko\Bridge\Akeneo\Magento\Scope as ScopeInterface;

class ScopeMapping implements ScopeInterface
{
    /** @var Scope */
    private $scope;
    /** @var MagentoStore */
    private $store;

    public function __construct(Scope $scope, MagentoStore $store)
    {
        $this->scope = $scope;
        $this->store = $store;
    }

    public function locales(): iterable
    {
        return $this->scope->locales();
    }

    public function code(): string
    {
        return $this->scope->code();
    }

    public function store(): MagentoStore
    {
        return $this->store;
    }
}