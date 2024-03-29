<?php

namespace App\Infrastructure;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\AxisAttributeList;
use App\Domain\Magento\Family;
use App\Domain\Magento\FamilyVariant;
use App\Domain\Magento\FamilyVariantAxis;

class VariantAxisesFactory
{
    /** @var AttributeRenderer[] */
    private $renderers;

    public function __construct(AttributeRenderer ...$renderers)
    {
        $this->renderers = $renderers;
    }

    public function __invoke(array $config): array
    {
        return iterator_to_array($this->walk($config));
    }

    private function walk(array $config): \Iterator
    {
        foreach ($config['families'] as $familyConfig) {
            $family = new Family($familyConfig['code'], ...$this->extractAttributes($familyConfig['attributes']));
            foreach ($familyConfig['variations'] as $variation) {
                if (isset($variation['level_2']['axis'])) {
                    yield new FamilyVariant(
                        $family,
                        $variation['code'],
                        $variation['skuPattern'],
                        new FamilyVariantAxis(
                            new AxisAttributeList(...$this->extractAttributes($variation['level_1']['axis'])),
                            ...$this->extractAttributes($variation['level_1']['attributes'])
                        ),
                        new FamilyVariantAxis(
                            new AxisAttributeList(...$this->extractAttributes($variation['level_2']['axis'])),
                            ...$this->extractAttributes($variation['level_2']['attributes'])
                        )
                    );
                } else {
                    yield new FamilyVariant(
                        $family,
                        $variation['code'],
                        null,
                        new FamilyVariantAxis(
                            new AxisAttributeList(...$this->extractAttributes($variation['level_1']['axis'])),
                            ...$this->extractAttributes($variation['level_1']['attributes'])
                        )
                    );
                }
            }
        }
    }

    /**
     * @param string[] $axises
     *
     * @return \Iterator|AttributeRenderer[]
     */
    private function extractAttributes(array $axises): \Iterator
    {
        foreach ($axises as $code) {
            yield $this->findAttributeRenderer($code);
        }
    }

    private function findAttributeRenderer(string $code): AttributeRenderer
    {
        $renderers = array_filter($this->renderers, function (AttributeRenderer $renderer) use ($code) {
            return $renderer->attribute()->code() === $code;
        });

        if (count($renderers) > 1) {
            throw new AttributeNotFoundException(strtr(
                'Found several attributes configuration with code "%code%".',
                [
                    '%code%' => $code,
                ]
            ));
        }

        if (count($renderers) < 1) {
            throw new AttributeNotFoundException(strtr(
                'Attribute with code "%code%" was not found in configuration.',
                [
                    '%code%' => $code,
                ]
            ));
        }

        return array_pop($renderers);
    }
}