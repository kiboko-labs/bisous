<?php

namespace Kiboko\Bridge\Akeneo\Magento\Locale;

use Kiboko\Bridge\Akeneo\Magento\Locale as LocaleInterface;
use Kiboko\Bridge\Akeneo\Magento\MagentoStore;

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