<?php

namespace spec\App\Infrastructure;

use App\Domain\Magento\Attribute\AdHoc;
use App\Domain\Magento\AttributeRenderer\Identifier;
use App\Domain\Magento\AttributeRenderer\SimpleSelect;
use App\Domain\Magento\AttributeRenderer\Text;
use App\Domain\Magento\AttributeRenderer\TextArea;
use App\Domain\Magento\AxisAttributeList;
use App\Domain\Magento\Family;
use App\Domain\Magento\FamilyVariant;
use App\Domain\Magento\FamilyVariantAxis;
use App\Domain\Magento\FieldResolver\Globalised;
use PhpSpec\ObjectBehavior;

class FamilyVariantMasterAttributeAggregatorSpec extends ObjectBehavior
{
    function it_will_return_attributes_in_two_levels_axises()
    {
        $attributes = [
            $sku = new Identifier(
                new AdHoc('sku', 'general'),
                new Globalised()
            ),
            $name = new Text(
                new AdHoc('name', 'general'),
                new Globalised()
            ),
            $variantName = new Text(
                new AdHoc('variant_name', 'general'),
                new Globalised()
            ),
            $description = new TextArea(
                new AdHoc('description', 'general'),
                new Globalised()
            ),
            $variantDescription = new TextArea(
                new AdHoc('variant_description', 'general'),
                new Globalised()
            ),
            $color = new SimpleSelect(
                new AdHoc('color', 'general'),
                new Globalised()
            ),
            $size = new SimpleSelect(
                new AdHoc('size', 'general'),
                new Globalised()
            ),
        ];

        $variant = new FamilyVariant(
            new Family(
                'jeans',
                ...$attributes
            ),
            'jeans_by_color_and_size',
            null,
            new FamilyVariantAxis(
                new AxisAttributeList($size),
                $variantName,
                $variantDescription
            ),
            new FamilyVariantAxis(
                new AxisAttributeList($color),
                $sku
            )
        );

        $this->__invoke($variant)->shouldIterateAs(new \ArrayIterator([
            $name,
            $description
        ]));
    }
}
