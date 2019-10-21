<?php

namespace App\Infrastructure;

use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\Family;
use App\Domain\Magento\FamilyVariant;

class FamiliesFactory
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
        foreach ($config['families'] as $family) {
            yield new Family(
                $family['code'],
                ...$this->extractAttributes($family['attributes'])
            );
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