<?php

namespace Kiboko\Bridge\Akeneo\Magento;

interface CodeGenerator
{
    public function table(): string;
    public function default(): string;
    public function alias(): string;
    public function column(): string;
}