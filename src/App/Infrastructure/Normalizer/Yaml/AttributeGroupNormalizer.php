<?php

namespace App\Infrastructure\Normalizer\Yaml;

use App\Domain\Configuration\DTO\AttributeGroup;
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

class AttributeGroupNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /** @var NormalizerInterface */
    private $normalizer;

    public function __construct(
        NormalizerInterface $normalizer,
        DenormalizerInterface $denormalizer
    ) {
        $this->normalizer = new ListNormalizer($normalizer, $denormalizer);
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