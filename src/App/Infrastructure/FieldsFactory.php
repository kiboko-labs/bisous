<?php

namespace App\Infrastructure;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\Field;
use App\Domain\Magento\FieldResolver;
use App\Domain\Magento\Locale;
use App\Domain\Magento\Scope;

/** @deprecated  */
class FieldsFactory
{
    /** @var Attribute[] */
    private $attributes;
    /** @var Scope[] */
    private $scopes;
    /** @var Locale[] */
    private $locales;
    /** @var Attribute[] */
    private $axises;

    public function __construct(
        array $attributes,
        array $scopes,
        array $locales,
        array $axises
    ) {
        throw new \Error('Deprecated class ' . __CLASS__);
        $this->attributes = $attributes;
        $this->scopes = $scopes;
        $this->locales = $locales;
        $this->axises = $axises;
    }

    /**
     * @return Field[]
     */
    public function __invoke(array $config): array
    {
        return iterator_to_array($this->flatten($config));
    }

    private function flatten(array $config): \Iterator
    {
        foreach ($this->map($config) as $group) {
            yield from $group;
        }
    }

    private function map(array $config): iterable
    {
        return array_map(function (array $config) {
            try {
                $attribute = $this->findAxis($config['code']);
                yield from (new FieldResolver\VariantAxis())
                    ->fields($attribute);
                return;
            } catch (AttributeNotFoundException $e) {
                if ((isset($config['localised']) && $config['localised'] === true) &&
                    (isset($config['scoped']) && $config['scoped'] === true)
                ) {
                    yield from (new FieldResolver\ScopedAndLocalized())
                        ->fields($this->findAttribute($config['code']));
                } else if ((!isset($config['localised']) || $config['localised'] !== true) &&
                    (isset($config['scoped']) && $config['scoped'] === true)
                ) {
                    yield from (new FieldResolver\Scoped())
                        ->fields($this->findAttribute($config['code']));
                } else if ((isset($config['localised']) && $config['localised'] === true) &&
                    (!isset($config['scoped']) || $config['scoped'] !== true)
                ) {
                    yield from (new FieldResolver\Localized())
                        ->fields($this->findAttribute($config['code']));
                } else {
                    yield from (new FieldResolver\Globalised())
                        ->fields($this->findAttribute($config['code']));
                }
            }
        }, $config['attributes']);
    }

    private function findAttribute(string $code): Attribute
    {
        $attributes = array_filter($this->attributes, function (Attribute $attribute) use ($code) {
            return $attribute->code() === $code;
        });

        if (count($attributes) > 1) {
            throw new AttributeNotFoundException(strtr(
                'Found several attributes configuration with code "%code%".',
                [
                    '%code%' => $code,
                ]
            ));
        }

        if (count($attributes) < 1) {
            throw new AttributeNotFoundException(strtr(
                'Attribute with code "%code%" was not found in configuration.',
                [
                    '%code%' => $code,
                ]
            ));
        }

        return array_pop($attributes);
    }

    private function findAxis(string $code): Attribute
    {
        $attributes = array_filter($this->axises, function (Attribute $attribute) use ($code) {
            return $attribute->code() === $code;
        });

        if (count($attributes) > 1) {
            throw new AttributeNotFoundException(strtr(
                'Found several axis attributes configuration with code "%code%".',
                [
                    '%code%' => $code,
                ]
            ));
        }

        if (count($attributes) < 1) {
            throw new AttributeNotFoundException(strtr(
                'Attribute axis with code "%code%" was not found in configuration.',
                [
                    '%code%' => $code,
                ]
            ));
        }

        return array_pop($attributes);
    }
}