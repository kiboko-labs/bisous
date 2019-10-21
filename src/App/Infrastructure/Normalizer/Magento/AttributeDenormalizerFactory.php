<?php

namespace App\Infrastructure\Normalizer\Magento;

use App\Infrastructure\Normalizer\ListDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AttributeDenormalizerFactory
{
    public function __invoke(): DenormalizerInterface
    {
        return new ListDenormalizer(
            new AttributeDenormalizer(
                new Attribute\AdHocNormalizer(),
                new Attribute\ExNihiloNormalizer(),
                new Attribute\AliasedNormalizer()
            )
        );
    }
}