<?php

namespace App\Domain\Magento\FieldResolver;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\CodeGenerator;
use App\Domain\Magento\Field;
use App\Domain\Magento\FieldResolver;
use App\Domain\Magento\LocaleStore;

class Localized implements FieldResolver
{
    /** @var LocaleStore[] */
    private $locales;

    public function __construct(LocaleStore ...$locales)
    {
        $this->locales = $locales;
    }

    public function fields(Attribute $attribute): iterable
    {
        return iterator_to_array((function(Attribute $attribute) {
            foreach ($this->locales as $locale) {
                yield new Field(
                    new CodeGenerator\Localized(
                        $attribute,
                        $locale
                    ),
                    $locale->store()
                );
            }
        })($attribute));
    }

    public function template(AttributeRenderer $renderer): string
    {
        return strtr('product/attribute/extract-{{ type }}-localizable.sql.twig', [
            '{{ type }}' => $renderer,
        ]);
    }
}