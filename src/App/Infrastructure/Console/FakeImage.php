<?php declare(strict_types=1);

namespace App\Infrastructure\Console;

abstract class FakeImage implements Image
{
    private $images;

    public function __construct(int $count = 10)
    {
        $count = max($count, 0);
        $images = new \SplFixedArray($count);

        for ($i = 0; $i < $count; ++$i) {
            $images[$i] = file_get_contents($this->url());
        }

        $this->images = new \InfiniteIterator($images);
        $this->images->rewind();
    }

    abstract public function url(): string;

    public function __toString()
    {
        try {
            return $this->images->current();
        } finally {
            $this->images->next();
        }
    }
}