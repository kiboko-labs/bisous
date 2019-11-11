<?php declare(strict_types=1);

namespace App\Infrastructure\Console;

final class RandomJpeg extends RandomImage
{
    public function url(): string
    {
        return 'https://placeimg.com/640/480/tech';
    }
}