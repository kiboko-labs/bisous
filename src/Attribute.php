<?php

namespace Kiboko\Bridge\Akeneo\Magento;

interface Attribute
{
    public function codeInSource(): string;
    public function codeInDestination(): string;
}