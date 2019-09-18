<?php

namespace App\Infrastructure\Normalizer\Yaml;

use App\Domain\Configuration\DTO\Family;
use App\Domain\Configuration\DTO\Label;
use App\Infrastructure\Normalizer\ListNormalizer;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FamilyNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /** @var NormalizerInterface */
    private $labelNormalizer;
    /** @var DenormalizerInterface */
    private $labelDenormalizer;
    /** @var ListNormalizer */
    private $attributeGroupNormalizer;

    public function __construct(
        NormalizerInterface $labelNormalizer,
        DenormalizerInterface $labelDenormalizer,
        NormalizerInterface $attributeGroupNormalizer,
        DenormalizerInterface $attributeGroupDenormalizer
    ) {
        $this->labelNormalizer = $labelNormalizer;
        $this->labelDenormalizer = $labelDenormalizer;
        $this->attributeGroupNormalizer = new ListNormalizer($attributeGroupNormalizer, $attributeGroupDenormalizer);
    }

    /**
     * @param Family|mixed $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'code' => $object->code,
            'label' => $object->label,
            'attributes' => $object->attributeGroups,
            'variations' => []
        ];
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return new Family(
            $data['code'],
            $this->labelDenormalizer->denormalize(
                $data['label'] ?? ucfirst(str_replace('_', ' ', $data['code'])),
                Label::class,
                $format,
                $context
            ),
            ...$this->attributeGroupNormalizer->denormalize(
                $data['label'] ?? ucfirst(str_replace('_', ' ', $data['code'])),
                Label::class,
                $format,
                $context
            )
        );
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Family
            && $format === 'yaml';
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_a($type, Family::class)
            && $format === 'yaml';
    }
}