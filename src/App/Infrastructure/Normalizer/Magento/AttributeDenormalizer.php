<?php

namespace App\Infrastructure\Normalizer\Magento;

use App\Infrastructure\Normalizer\NoSuitableDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AttributeDenormalizer implements DenormalizerInterface
{
    /** @var DenormalizerInterface[] */
    private $strategiesDenormalizers;

    public function __construct(DenormalizerInterface ...$strategiesDenormalizers)
    {
        $this->strategiesDenormalizers = $strategiesDenormalizers;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $denormalizer = $this->findDenormalizer($data, $type, $format);

        return $denormalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        try {
            $this->findDenormalizer($data, $type, $format);
        } catch (NoSuitableDenormalizer $e) {
            return false;
        }

        return true;
    }

    private function findDenormalizer($data, $type, $format = null, array $context = []): DenormalizerInterface
    {
        foreach ($this->strategiesDenormalizers as $denormalizer) {
            if ($denormalizer->supportsDenormalization($data, $type, $format)) {
                return $denormalizer;
            }
        }

        throw new NoSuitableDenormalizer(isset($data['strategy']) ?
            strtr(
                'There is no available denormalizer for this attribute strategy "%strategy%".',
                [
                    '%strategy%' => $data['strategy'],
                ]
            ) :
            'There is no available denormalizer for this attribute strategy.'
        );
    }
}