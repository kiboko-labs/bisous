<?php

namespace Kiboko\Bridge\Akeneo\Magento\FieldResolver;

use Kiboko\Bridge\Akeneo\Magento\Attribute;
use Kiboko\Bridge\Akeneo\Magento\AttributeRenderer;
use Kiboko\Bridge\Akeneo\Magento\CodeGenerator;
use Kiboko\Bridge\Akeneo\Magento\Field;
use Kiboko\Bridge\Akeneo\Magento\FieldResolver;
use Kiboko\Bridge\Akeneo\Magento\Locale;
use Kiboko\Bridge\Akeneo\Magento\MagentoStore;

class Localized implements FieldResolver
{
    /** @var Locale[] */
    private $locales;

    public function __construct(Locale ...$locales)
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