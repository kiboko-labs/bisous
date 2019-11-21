<?php

namespace App\Infrastructure\Normalizer\Magento;

use App\Domain\Magento\Locale;
use App\Domain\Magento\LocaleStore;
use App\Domain\Magento\MagentoStore;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class LocaleStoreDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return new Locale\LocaleStore($data['code'], new MagentoStore($data['store']));
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return isset($data['code'])
            && is_string($data['code'])
            && isset($data['store'])
            && is_numeric($data['store'])
            && $type === LocaleStore::class;
    }
}