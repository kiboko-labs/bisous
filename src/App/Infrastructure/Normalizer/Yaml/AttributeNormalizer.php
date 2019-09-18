<?php

namespace App\Infrastructure\Normalizer\Yaml;

use App\Domain\Configuration\DTO\Attribute;
use App\Domain\Configuration\DTO\Label;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AttributeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /** @var NormalizerInterface */
    private $labelNormalizer;
    /** @var DenormalizerInterface */
    private $labelDenormalizer;

    public function __construct(
        NormalizerInterface $labelNormalizer,
        DenormalizerInterface $labelDenormalizer
    ) {
        $this->labelNormalizer = $labelNormalizer;
        $this->labelDenormalizer = $labelDenormalizer;
    }

    /**
     * @param Attribute $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'code' => $object->code,
            'label' => $this->labelNormalizer->normalize($object->label, $format, $context),
            'strategy' => $object->strategy,
            'type' => $object->type,
        ];
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return new Attribute(
            $data['code'],
            $this->labelDenormalizer->denormalize(
                $data['label'] ?? ucfirst(str_replace('_', ' ', $data['code'])),
                Label::class,
                $format,
                $context
            ),
            $data['strategy'],
            $data['type']
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Attribute
            && $format === 'yaml';
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_a($type, Attribute::class)
            && $format === 'yaml';
    }
}