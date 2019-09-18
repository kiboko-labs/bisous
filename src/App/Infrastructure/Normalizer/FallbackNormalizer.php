<?php

namespace App\Infrastructure\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FallbackNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        // TODO: Implement normalize() method.
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        // TODO: Implement denormalize() method.
    }

    public function supportsNormalization($data, $format = null)
    {
        // TODO: Implement supportsNormalization() method.
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        // TODO: Implement supportsDenormalization() method.
    }
}