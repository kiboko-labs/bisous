<?php declare(strict_types=1);

namespace App\Infrastructure\Console;

final class RandomPng extends RandomImage
{
    public function url(): string
    {
        return 'http://placehold.jp/300x200.png';
    }
}