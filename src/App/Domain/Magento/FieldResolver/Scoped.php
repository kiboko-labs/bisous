<?php

namespace App\Domain\Magento\FieldResolver;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\CodeGenerator;
use App\Domain\Magento\Field;
use App\Domain\Magento\FieldResolver;
use App\Domain\Magento\Scope;

class Scoped implements FieldResolver
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
                yield new Field(
                    new CodeGenerator\Scoped(
                        $attribute,
                        $scope
                    ),
                    $scope->store()
                );
            }
        })($attribute));
    }

    public function template(AttributeRenderer $renderer): string
    {
        return strtr('product/attribute/extract-{{ type }}-scopable.sql.twig', [
            '{{ type }}' => $renderer,
        ]);
    }
}