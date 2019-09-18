<?php

namespace App\Infrastructure\Iteration;

/**
 * @property \MultipleIterator $inner
 */
class MapIterator extends AbstractCallbackIterator
{
    /** @var \Traversable[] */
    private $innerIterators;
    /** @var int */
    private $index;

    public function __construct(callable $callback, \Traversable ...$traversables)
    {
        $this->innerIterators = [];
        $this->index = 0;

        parent::__construct(new \MultipleIterator(
            \MultipleIterator::MIT_NEED_ANY | \MultipleIterator::MIT_KEYS_NUMERIC
        ), $callback);

        $this->attachIterator(...$traversables);
    }

    public function attachIterator(\Traversable ...$traversables): void
    {
        foreach ($traversables as $traversable) {
            if ($traversable instanceof \Iterator) {
                $this->inner->attachIterator($traversable);
            } else {
                $this->inner->attachIterator(new \IteratorIterator($traversable));
            }

            $this->innerIterators[] = $traversable;
        }
    }

    public function rewind()
    {
        $this->index = 0;
        parent::rewind();
    }

    public function next()
    {
        $this->index++;
        parent::next();
    }

    public function key()
    {
        parent::key();
        return $this->index;
    }

    public function current()
    {
        return ($this->callback)(...$this->inner->current());
    }
}
