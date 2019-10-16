<?php

namespace App\Infrastructure\Normalizer\Magento\Attribute;

use App\Domain\Magento\Attribute;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AdHocNormalizer implements DenormalizerInterface
{
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return new Attribute\AdHoc($data['code']);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return isset($data['strategy'])
            && isset($data['code'])
            && $data['strategy'] === 'ad-hoc'
            && ($type === Attribute::class || $type === Attribute\AdHoc::class);
    }
}