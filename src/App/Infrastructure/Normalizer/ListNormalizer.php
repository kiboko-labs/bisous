<?php

namespace App\Infrastructure\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ListNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /** @var NormalizerInterface */
    private $normalizer;
    /** @var DenormalizerInterface */
    private $denormalizer;

    public function __construct(
        NormalizerInterface $normalizer,
        DenormalizerInterface $denormalizer
    ) {
        $this->normalizer = $normalizer;
        $this->denormalizer = $denormalizer;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return iterator_to_array($this->normalizeChild($object, substr($format, 0, -2), $context));
    }

    public function normalizeChild(iterable $object, $format, array $context): \Iterator
    {
        $childFormat = substr($format, 0, -2);
        foreach ($object as $item) {
            yield $this->normalizer->normalize($item, $childFormat, $context);
        }
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return iterator_to_array($this->denormalizeChild($data, substr($type, 0, -2), $format, $context));
    }

    public function denormalizeChild(iterable $object, $type, $format, array $context): \Iterator
    {
        $childType = substr($type, 0, -2);
        foreach ($object as $item) {
            yield $this->denormalizer->denormalize($item, $childType, $format, $context);
        }
    }

    public function supportsNormalization($data, $format = null)
    {
        return is_iterable($data);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return substr($type, -2) === '[]'
            && $this->denormalizer->supportsDenormalization($data, substr($type, 0, -2));
    }
}