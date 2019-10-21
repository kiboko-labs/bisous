<?php

namespace App\Domain\Magento;

interface FieldResolver
{
    public function fields(Attribute $attribute): iterable;
    public function template(AttributeRenderer $renderer): string;
}