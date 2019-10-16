<?php

namespace App\Domain\Magento;

interface CodeGenerator
{
    public function attribute(): string;
    public function table(): string;
    public function default(): string;
    public function alias(): string;
    public function column(): string;
}