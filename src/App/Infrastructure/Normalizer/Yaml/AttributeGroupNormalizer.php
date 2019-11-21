<?php

namespace App\Infrastructure\Normalizer\Yaml;

use App\Domain\Configuration\DTO\AttributeGroup;
use App\Infrastructure\Normalizer\ListNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AttributeGroupNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /** @var NormalizerInterface */
    private $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = new ListNormalizer($normalizer);
    }

    /**
     * @param AttributeGroup|mixed $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'code' => $object->code,
            'code' => $this->normalizer->normalize($object->attributeGroups, $format, $context),
        ];
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        // TODO: Implement denormalize() method.
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AttributeGroup
            && $format === 'yaml';
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        // TODO: Implement supportsDenormalization() method.
    }
}