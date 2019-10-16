<?php

namespace App\Infrastructure\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ListDenormalizer implements DenormalizerInterface
{
    /** @var DenormalizerInterface */
    private $denormalizer;

    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return iterator_to_array($this->denormalizeChild($data, substr($type, 0, -2), $format, $context));
    }

    private function denormalizeChild(iterable $object, $type, $format, array $context): \Iterator
    {
        foreach ($object as $item) {
            yield $this->denormalizer->denormalize($item, $type, $format, $context);
        }
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return substr($type, -2) === '[]'
            && $this->denormalizer->supportsDenormalization($data, substr($type, 0, -2));
    }
}