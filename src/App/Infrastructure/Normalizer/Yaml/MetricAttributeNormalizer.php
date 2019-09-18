<?php

namespace App\Infrastructure\Normalizer\Yaml;

use App\Domain\Configuration\DTO\MetricAttribute;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MetricAttributeNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {

    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof MetricAttribute;
    }
}