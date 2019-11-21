<?php

namespace App\Infrastructure;

use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\FamilyVariant;

class FamilyVariantMasterAttributeAggregator
{
    public function all(FamilyVariant ...$variants): array
    {
        $attributes = $this->attributes(...$variants);
        return array_values(array_filter(
            array_values(iterator_to_array($this->walk(...$variants), false)),
            function (AttributeRenderer $attribute) use ($attributes) {
                return !in_array($attribute->attribute()->code(), $attributes);
            }
        ));
    }

    private function attributes(FamilyVariant ...$variants): array
    {
        return array_unique(
            array_map(
                function (AttributeRenderer $attribute) {
                    return $attribute->attribute()->code();
                },
                (new FamilyVariantAxisAttributeAggregator())(...$variants)
            ),
            SORT_REGULAR
        );
    }

    private function walk(FamilyVariant ...$variants): \Iterator
    {
        foreach ($variants as $variant) {
            yield from $variant->family->attributes;
        }
    }
}