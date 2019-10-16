<?php

namespace App\Infrastructure;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\FieldResolver;

class AttributeRendererFactory
{
    /**
     * @param Attribute[] $attributes
     * @param Attribute[] $axisAttributes
     * @param array $config
     *
     * @return AttributeRenderer[]
     */
    public function __invoke(iterable $attributes, iterable $axisAttributes, array $config): array
    {
        return iterator_to_array($this->walk($attributes, $axisAttributes, $config));
    }

    /**
     * @param Attribute[] $attributes
     * @param Attribute[] $axisAttributes
     * @param array $config
     *
     * @return AttributeRenderer[]|\Iterator
     */
    public function walk(iterable $attributes, iterable $axisAttributes, array $config): \Iterator
    {
        foreach ($attributes as $attribute) {
            if (!isset($config[$attribute->code()])) {
                continue;
            }

            $attributeSpec = $config[$attribute->code()];

            switch ($attributeSpec['type']) {
                case 'datetime':
                    yield new AttributeRenderer\Datetime(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false
                        )
                    );
                    break;

                case 'simple-select':
                    yield new AttributeRenderer\SimpleSelect(
                        $attribute,
                        $this->buildFieldResolver(
                            in_array($attribute, $axisAttributes),
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false
                        )
                    );
                    break;

                case 'image':
                    yield new AttributeRenderer\Image(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false
                        )
                    );
                    break;

                case 'status':
                    yield new AttributeRenderer\Status(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false
                        )
                    );
                    break;

                case 'text':
                    yield new AttributeRenderer\Text(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false
                        )
                    );
                    break;

                case 'varchar':
                    yield new AttributeRenderer\Varchar(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false
                        )
                    );
                    break;

                case 'visibility':
                    yield new AttributeRenderer\Visibility(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false
                        )
                    );
                    break;
            }
        }
    }

    private function buildFieldResolver(bool $isAxis, bool $scoped, bool $localised): FieldResolver
    {
        if ($isAxis === true) {
            return new FieldResolver\VariantAxis();
        }
        if ($localised === true && $scoped === true) {
            return new FieldResolver\ScopedAndLocalized();
        }
        if ($localised !== true && $scoped === true) {
            return new FieldResolver\Scoped();
        }
        if ($localised === true && $scoped !== true) {
            return new FieldResolver\Localized();
        }

        return new FieldResolver\Globalised();
    }
}