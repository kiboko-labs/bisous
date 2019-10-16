<?php

namespace App\Infrastructure\Normalizer\Magento;

use App\Infrastructure\Normalizer\ListDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class LocaleDenormalizerFactory
{
    public function __invoke(): DenormalizerInterface
    {
        return new ListDenormalizer(
            new LocaleDenormalizer()
        );
    }
}