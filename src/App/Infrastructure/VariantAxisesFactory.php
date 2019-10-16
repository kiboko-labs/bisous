<?php

namespace App\Infrastructure;

use App\Domain\Magento\Attribute;

class VariantAxisesFactory
{
    /** @var Attribute[] */
    private $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function __invoke(array $config): array
    {
        $codes = [];
        foreach (array_unique(iterator_to_array($this->walk($config))) as $code) {
            array_push($codes, $this->findAttribute($code));
        }

        return $codes;
    }

    private function walk(array $config): \Iterator
    {
        foreach ($config['families'] as $family) {
            foreach ($family['variations'] as $variation) {
                yield from $variation['level_1']['axis'];

                if (isset($variation['level_2']['axis'])) {
                    yield from $variation['level_2']['axis'];
                }
            }
        }
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
}