<?php declare(strict_types=1);

namespace App\Infrastructure\Console;

final class FakeJpeg extends FakeImage
{
    public function url(): string
    {
        return 'https://placeimg.com/640/480/tech';
    }
}