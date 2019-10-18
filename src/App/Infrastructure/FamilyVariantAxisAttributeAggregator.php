<?php

namespace App\Infrastructure;

use App\Domain\Magento\FamilyVariant;

class FamilyVariantAxisAttributeAggregator
{
    public function __invoke(FamilyVariant ...$variants): iterable
    {
        return array_unique(iterator_to_array($this->walk(...$variants)));
    }

    private function walk(FamilyVariant ...$variants): \Iterator
    {
        foreach ($variants as $variant) {
            yield from $variant->axis(1)->attributes;
            if ($variant->isTwoLevels()) {
                yield from $variant->axis(2)->attributes;
            }
        }
    }
}