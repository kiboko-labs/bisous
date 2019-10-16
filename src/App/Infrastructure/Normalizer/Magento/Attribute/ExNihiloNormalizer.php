<?php

namespace App\Infrastructure\Normalizer\Magento\Attribute;

use App\Domain\Magento\Attribute;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ExNihiloNormalizer implements DenormalizerInterface
{
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return new Attribute\ExNihilo($data['code']);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return isset($data['strategy'])
            && isset($data['code'])
            && $data['strategy'] === 'ex-nihilo'
            && ($type === Attribute::class || $type === Attribute\ExNihilo::class);
    }
}