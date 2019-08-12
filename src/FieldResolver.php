<?php

namespace Kiboko\Bridge\Akeneo\Magento;

interface FieldResolver
{
    public function fields(Attribute $attribute): iterable;
    public function template(AttributeRenderer $renderer);
}