<?php

namespace App\Infrastructure\Normalizer\Yaml;

use App\Domain\Configuration\DTO\Label;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LabelNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        return (string) $object;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return new Label($data);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Label
            && $format == 'yaml';
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_a($type, Label::class)
            && $format == 'yaml';
    }
}