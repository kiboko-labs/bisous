<?php

namespace App\Infrastructure\Normalizer\Magento;

use App\Infrastructure\Normalizer\ListDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ScopeDenormalizerFactory
{
    public function __invoke(): DenormalizerInterface
    {
        return new ListDenormalizer(
            new ScopeDenormalizer(
                new ListDenormalizer(
                    new LocaleStoreDenormalizer()
                )
            )
        );
    }
}