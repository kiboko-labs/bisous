<?php

namespace App\Domain\Magento;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SqlExportTwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('as_field_alias', function(Field $field) {
                return $field->codeGenerator->alias();
            }),
            new TwigFunction('as_field_column', function(Field $field) {
                return $field->codeGenerator->column();
            }),
            new TwigFunction('as_field_table', function(Field $field) {
                return $field->codeGenerator->table();
            }),

            new TwigFunction('as_default_alias', function() {
                return 'attr_default';
            }),

            new TwigFunction('as_attribute_axis_table', function(FamilyVariant $variant, Attribute $attribute) {
                return strtr(
                    'tmp_axis_{{ variant }}__{{ attribute }}',
                    [
                        '{{ variant }}' => $variant->code,
                        '{{ attribute }}' => $attribute->code(),
                    ]
                );
            }),
            new TwigFunction('as_attribute_alias', function(Attribute $attribute) {
                return strtr(
                    'attr_default_{{ attribute }}',
                    [
                        '{{ attribute }}' => $attribute->code(),
                    ]
                );
            }),
            new TwigFunction('as_attribute_table', function(Attribute $attribute) {
                return strtr(
                    'tmp_{{ attribute }}',
                    [
                        '{{ attribute }}' => $attribute->code(),
                    ]
                );
            }),
            new TwigFunction('as_attribute_default_table', function(Attribute $attribute) {
                return strtr(
                    'tmp_{{ attribute }}_default',
                    [
                        '{{ attribute }}' => $attribute->code(),
                    ]
                );
            }),
            new TwigFunction('as_attribute_localized_table', function(Attribute $attribute, Locale $locale) {
                return strtr(
                    'tmp_{{ attribute }}_{{ locale }}',
                    [
                        '{{ attribute }}' => $attribute->code(),
                        '{{ locale }}' => $locale->code(),
                    ]
                );
            }),
            new TwigFunction('as_attribute_scoped_table', function(Attribute $attribute, Scope $scope) {
                return strtr(
                    'tmp_{{ attribute }}_{{ scope }}',
                    [
                        '{{ attribute }}' => $attribute->code(),
                        '{{ scope }}' => $scope->code(),
                    ]
                );
            }),
            new TwigFunction('as_attribute_scoped_and_localized_table', function(Attribute $attribute, Scope $scope, Locale $locale) {
                return strtr(
                    'tmp_{{ attribute }}_{{ scope }}_{{ locale }}',
                    [
                        '{{ attribute }}' => $attribute->code(),
                        '{{ scope }}' => $scope->code(),
                        '{{ locale }}' => $locale->code(),
                    ]
                );
            }),

            new TwigFunction('as_product_hierarchy_sku_field', function(string $skuField, FamilyVariant $family) {
                $replacements = [
                    '%parent%' => $skuField,
                ];

                foreach ($family->axis(1)->attributes as $attribute) {
                    /** @var Field $field */
                    foreach ($attribute->fields() as $field) {
                        $replacements['%' . $attribute->attribute()->code() . '%'] = strtr(
                            '{{ alias }}.{{ field }}',
                            [
                                '{{ alias }}' => $field->codeGenerator->alias(),
                                '{{ field }}' => $field->codeGenerator->column(),
                            ]
                        );
                    }
                }

                $pattern = 'CONCAT("' . preg_replace_callback('/{{\s*([a-z_]+)\s*}}/', function($matches) {
                    return '", %' . $matches[1] . '%, "';
                }, $family->skuTemplate) . '")';

                return strtr($pattern, $replacements);
            }),
            new TwigFunction('as_product_hierarchy_axis_alias', function(Attribute $attribute) {
                return strtr(
                    'attr_{{ attribute }}',
                    [
                        '{{ attribute }}' => $attribute->code(),
                    ]
                );
            }),
        ];
    }
}