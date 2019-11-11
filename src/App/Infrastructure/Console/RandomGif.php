<?php declare(strict_types=1);

namespace App\Infrastructure\Console;

final class RandomGif extends RandomImage
{
    public function url(): string
    {
        return 'https://www.placecage.com/gif/200/300';
    }
}