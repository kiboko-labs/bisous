<?php declare(strict_types=1);

namespace App\Infrastructure\Console;

final class FakePng extends FakeImage
{
    public function url(): string
    {
        return 'http://placehold.jp/300x200.png';
    }
}