<?php

namespace App\Domain\Magento\FieldResolver;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\CodeGenerator;
use App\Domain\Magento\Field;
use App\Domain\Magento\FieldResolver;
use App\Domain\Magento\MagentoStore;

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