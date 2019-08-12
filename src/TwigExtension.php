<?php

namespace Kiboko\Bridge\Akeneo\Magento;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getfunctions()
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

            new TwigFunction('as_attribute_table', function(Attribute $attribute) {
                return strtr(
                    'tmp_{{ attribute }}',
                    [
                        '{{ attribute }}' => $attribute->codeInDestination(),
                    ]
                );
            }),
            new TwigFunction('as_attribute_default_table', function(Attribute $attribute) {
                return strtr(
                    'tmp_{{ attribute }}_default',
                    [
                        '{{ attribute }}' => $attribute->codeInDestination(),
                    ]
                );
            }),
            new TwigFunction('as_attribute_localized_table', function(Attribute $attribute, Locale $locale) {
                return strtr(
                    'tmp_{{ attribute }}_{{ locale }}',
                    [
                        '{{ attribute }}' => $attribute->codeInDestination(),
                        '{{ locale }}' => $locale->code(),
                    ]
                );
            }),
            new TwigFunction('as_attribute_scoped_table', function(Attribute $attribute, Scope $scope) {
                return strtr(
                    'tmp_{{ attribute }}_{{ scope }}',
                    [
                        '{{ attribute }}' => $attribute->codeInDestination(),
                        '{{ scope }}' => $scope->code(),
                    ]
                );
            }),
            new TwigFunction('as_attribute_scoped_and_localized_table', function(Attribute $attribute, Scope $scope, Locale $locale) {
                return strtr(
                    'tmp_{{ attribute }}_{{ scope }}_{{ locale }}',
                    [
                        '{{ attribute }}' => $attribute->codeInDestination(),
                        '{{ scope }}' => $scope->code(),
                        '{{ locale }}' => $locale->code(),
                    ]
                );
            }),
        ];
    }
}