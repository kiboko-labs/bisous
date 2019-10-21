<?php

namespace App\Domain\Magento\AttributeRenderer;

use App\Domain\Magento\FieldResolver;

/**
 * @property FieldResolver $fieldResolver
 */
trait ScopingAwareTrait
{
    public function isScoped(): bool
    {
        return $this->fieldResolver instanceof FieldResolver\Scoped
            || $this->fieldResolver instanceof FieldResolver\ScopedAndLocalized;
    }
}