<?php

namespace Kiboko\Bridge\Akeneo\Magento\FieldResolver;

use Kiboko\Bridge\Akeneo\Magento\Attribute;
use Kiboko\Bridge\Akeneo\Magento\AttributeRenderer;
use Kiboko\Bridge\Akeneo\Magento\CodeGenerator;
use Kiboko\Bridge\Akeneo\Magento\Field;
use Kiboko\Bridge\Akeneo\Magento\FieldResolver;
use Kiboko\Bridge\Akeneo\Magento\MagentoStore;

class Globalised implements FieldResolver
{
    public function fields(Attribute $attribute): iterable
    {
        return [
            new Field(
                new CodeGenerator\Globalised($attribute),
                new MagentoStore(0)
            ),
        ];
    }

    public function template(AttributeRenderer $renderer): string
    {
        return strtr('product/attribute/extract-{{ type }}.sql.twig', [
            '{{ type }}' => $renderer,
        ]);
    }
}