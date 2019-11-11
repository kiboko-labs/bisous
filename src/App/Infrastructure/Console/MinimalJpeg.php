<?php declare(strict_types=1);

namespace App\Infrastructure\Console;

class MinimalJpeg implements Image
{
    public function __toString()
    {
        return 'https://raw.githubusercontent.com/mathiasbynens/small/master/jpeg.jpg';
    }
}