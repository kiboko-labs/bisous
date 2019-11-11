<?php declare(strict_types=1);

namespace App\Infrastructure\Console;

final class FakeGif extends FakeImage
{
    public function url(): string
    {
        return 'https://www.placecage.com/gif/200/300';
    }
}