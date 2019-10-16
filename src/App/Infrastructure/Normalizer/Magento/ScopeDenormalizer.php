<?php

namespace App\Infrastructure\Normalizer\Magento;

use App\Domain\Magento\Locale;
use App\Domain\Magento\MagentoStore;
use App\Domain\Magento\Scope;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ScopeDenormalizer implements DenormalizerInterface
{
    /** @var DenormalizerInterface */
    private $localesDenormalizer;

    public function __construct(DenormalizerInterface $localesDenormalizer)
    {
        $this->localesDenormalizer = $localesDenormalizer;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return new Scope\Scope(
            $data['code'],
            new MagentoStore($data['store']),
            ...$this->localesDenormalizer->denormalize($data['locales'], Locale::class.'[]', $format, $context)
        );
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return isset($data['code'])
            && is_string($data['code'])
            && isset($data['store'])
            && is_numeric($data['store'])
            && isset($data['locales'])
            && is_array($data['locales'])
            && $type === Scope::class;
    }
}