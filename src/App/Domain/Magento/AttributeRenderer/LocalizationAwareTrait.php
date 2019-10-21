<?php

namespace App\Domain\Magento\AttributeRenderer;

use App\Domain\Magento\FieldResolver;

/**
 * @property FieldResolver $fieldResolver
 */
trait LocalizationAwareTrait
{
    public function isLocalized(): bool
    {
        return $this->fieldResolver instanceof FieldResolver\Localized
            || $this->fieldResolver instanceof FieldResolver\ScopedAndLocalized;
    }
}