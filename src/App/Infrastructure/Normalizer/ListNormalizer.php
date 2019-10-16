<?php

namespace App\Infrastructure\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ListNormalizer implements NormalizerInterface
{
    /** @var NormalizerInterface */
    private $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return iterator_to_array($this->normalizeChild($object, substr($format, 0, -2), $context));
    }

    private function normalizeChild(iterable $object, $format, array $context): \Iterator
    {
        foreach ($object as $item) {
            yield $this->normalizer->normalize($item, $format, $context);
        }
    }

    public function supportsNormalization($data, $format = null)
    {
        return is_iterable($data);
    }
}