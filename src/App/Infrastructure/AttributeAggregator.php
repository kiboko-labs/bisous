<?php

namespace App\Infrastructure;

use App\Domain\Magento\Family;

class AttributeAggregator
{
    public function __invoke(Family ...$families): iterable
    {
        return array_unique(iterator_to_array($this->walk(...$families)));
    }

    private function walk(Family ...$families): \Iterator
    {
        foreach ($families as $family) {
            yield from $family->attributes;
        }
    }
}