<?php

namespace App\Infrastructure;

use App\Domain\Magento\FamilyVariant;

class FamilyVariantLevelAxisAggregator
{
    public function __invoke(int $level, FamilyVariant ...$variants): array
    {
        return array_unique(
            iterator_to_array($this->walk($level, ...$variants), false),
            SORT_REGULAR
        );
    }

    private function walk(int $level, FamilyVariant ...$variants): \Iterator
    {
        foreach ($variants as $variant) {
            if ($level >= 2 && !$variant->isTwoLevels()) {
                continue;
            }
            yield from $variant->axis($level)->axises();
        }
    }
}