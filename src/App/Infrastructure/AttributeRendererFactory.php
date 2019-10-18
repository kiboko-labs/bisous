<?php

namespace App\Infrastructure;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\FieldResolver;
use App\Domain\Magento\Locale;
use App\Domain\Magento\Scope;

class AttributeRendererFactory
{
    /**
     * @param Attribute[] $attributes
     * @param Attribute[] $axisAttributes
     * @param Scope[] $scopes
     * @param Locale[] $locales
     * @param array $config
     *
     * @return AttributeRenderer[]
     */
    public function __invoke(
        iterable $attributes,
        iterable $axisAttributes,
        iterable $scopes,
        iterable $locales,
        array $config
    ): array {
        return iterator_to_array($this->walk($attributes, $axisAttributes, $scopes, $locales, $config));
    }

    /**
     * @param Attribute[] $attributes
     * @param Attribute[] $axisAttributes
     * @param Scope[] $scopes
     * @param Locale[] $locales
     * @param array $config
     *
     * @return AttributeRenderer[]|\Iterator
     */
    public function walk(
        iterable $attributes,
        iterable $axisAttributes,
        iterable $scopes,
        iterable $locales,
        array $config
    ): \Iterator {
        foreach ($attributes as $attribute) {
            if (!isset($config[$attribute->code()])) {
                continue;
            }

            $attributeSpec = $config[$attribute->code()];

            switch ($attributeSpec['type']) {
                case 'identifier':
                    yield new AttributeRenderer\Identifier(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            false,
                            false,
                            $scopes,
                            $locales
                        )
                    );
                    break;

                case 'datetime':
                    yield new AttributeRenderer\Datetime(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false,
                            $scopes,
                            $locales
                        )
                    );
                    break;

                case 'simple-select':
                    yield new AttributeRenderer\SimpleSelect(
                        $attribute,
                        $this->buildFieldResolver(
                            in_array($attribute, $axisAttributes),
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false,
                            $scopes,
                            $locales
                        )
                    );
                    break;

                case 'image':
                    yield new AttributeRenderer\Image(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false,
                            $scopes,
                            $locales
                        )
                    );
                    break;

                case 'status':
                    yield new AttributeRenderer\Status(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            true,
                            true,
                            $scopes,
                            $locales
                        )
                    );
                    break;

                case 'text-area':
                    yield new AttributeRenderer\TextArea(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false,
                            $scopes,
                            $locales
                        )
                    );
                    break;

                case 'rich-text':
                    yield new AttributeRenderer\RichText(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false,
                            $scopes,
                            $locales
                        )
                    );
                    break;

                case 'text':
                    yield new AttributeRenderer\Text(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            $attributeSpec['scoped'] ?? false,
                            $attributeSpec['localised'] ?? false,
                            $scopes,
                            $locales
                        )
                    );
                    break;

                case 'visibility':
                    yield new AttributeRenderer\Visibility(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            true,
                            true,
                            $scopes,
                            $locales
                        )
                    );
                    break;

                case 'metric':
                    yield new AttributeRenderer\Metric(
                        $attribute,
                        $this->buildFieldResolver(
                            false,
                            true,
                            true,
                            $scopes,
                            $locales
                        )
                    );
                    break;

                default:
                    throw new \UnexpectedValueException(strtr(
                        'Could not handle attribute of type %type%',
                        [
                            '%type%' => $attributeSpec['type']
                        ]
                    ));
                    break;
            }
        }
    }

    private function buildFieldResolver(
        bool $isAxis,
        bool $scoped,
        bool $localised,
        iterable $scopes,
        iterable $locales
    ): FieldResolver {
        if ($isAxis === true) {
            return new FieldResolver\VariantAxis();
        }
        if ($localised === true && $scoped === true) {
            return new FieldResolver\ScopedAndLocalized(...$scopes);
        }
        if ($localised !== true && $scoped === true) {
            return new FieldResolver\Scoped(...$scopes);
        }
        if ($localised === true && $scoped !== true) {
            return new FieldResolver\Localized(...$locales);
        }

        return new FieldResolver\Globalised();
    }
}