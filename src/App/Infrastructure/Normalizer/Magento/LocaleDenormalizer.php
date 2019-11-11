<?php

namespace App\Infrastructure\Normalizer\Magento;

use App\Domain\Magento\Locale;
use App\Domain\Magento\MagentoStore;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class LocaleDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return new Locale\Locale($data['code'], $data['currency'], new MagentoStore($data['store']));
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return isset($data['code'])
            && is_string($data['code'])
            && isset($data['currency'])
            && is_string($data['currency'])
            && isset($data['store'])
            && is_numeric($data['store'])
            && $type === Locale::class;
    }
}