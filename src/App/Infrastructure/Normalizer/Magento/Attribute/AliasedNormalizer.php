<?php

namespace App\Infrastructure\Normalizer\Magento\Attribute;

use App\Domain\Magento\Attribute;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AliasedNormalizer implements DenormalizerInterface
{
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return new Attribute\Aliased($data['code'], $data['source']);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return isset($data['strategy'])
            && isset($data['code'])
            && isset($data['source'])
            && $data['strategy'] === 'aliased'
            && ($type === Attribute::class || $type === Attribute\Aliased::class);
    }
}