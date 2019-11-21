<?php

namespace App\Infrastructure;

use App\Domain\Magento\FamilyVariant;

class FamilyVariantAxisAggregator
{
    public function __invoke(FamilyVariant ...$variants): array
    {
        return array_unique(
            iterator_to_array($this->walk(...$variants), false),
            SORT_REGULAR
        );
    }

    private function walk(FamilyVariant ...$variants): \Iterator
    {
        foreach ($variants as $variant) {
            yield from $variant->axis(1)->axises();
            if ($variant->isTwoLevels()) {
                yield from $variant->axis(2)->axises();
            }
        }
    }
}