<?php

namespace Kiboko\Bridge\Akeneo\Magento\FieldResolver;

use Kiboko\Bridge\Akeneo\Magento\Attribute;
use Kiboko\Bridge\Akeneo\Magento\AttributeRenderer;
use Kiboko\Bridge\Akeneo\Magento\CodeGenerator;
use Kiboko\Bridge\Akeneo\Magento\Field;
use Kiboko\Bridge\Akeneo\Magento\FieldResolver;
use Kiboko\Bridge\Akeneo\Magento\Scope;

class ScopedAndLocalized implements FieldResolver
{
    /** @var Scope[] */
    private $scopes;

    public function __construct(Scope ...$scopes)
    {
        $this->scopes = $scopes;
    }

    public function fields(Attribute $attribute): iterable
    {
        return iterator_to_array((function(Attribute $attribute) {
            foreach ($this->scopes as $scope) {
                foreach ($scope->locales() as $locale) {
                    yield new Field(
                        new CodeGenerator\ScopedAndLocalized(
                            $attribute,
                            $scope,
                            $locale
                        ),
                        $locale->store()
                    );
                }
            }
        })($attribute));
    }

    public function template(AttributeRenderer $renderer): string
    {
        return strtr('product/attribute/extract-{{ type }}-scopable-localizable.sql.twig', [
            '{{ type }}' => $renderer,
        ]);
    }
}