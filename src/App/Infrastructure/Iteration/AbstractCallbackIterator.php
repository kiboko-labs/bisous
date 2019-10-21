<?php

namespace App\Infrastructure\Iteration;

abstract class AbstractCallbackIterator implements \OuterIterator, \Countable
{
    /** @var \Iterator */
    protected $inner;
    /** @var callable */
    protected $callback;

    public function __construct(\Iterator $inner, callable $callback)
    {
        $this->inner = $inner;
        $this->callback = $callback;
    }

    public function next()
    {
        $this->inner->next();
    }

    public function key()
    {
        return $this->inner->key();
    }

    public function valid()
    {
        return $this->inner->valid();
    }

    public function rewind()
    {
        $this->inner->rewind();
    }

    public function getInnerIterator()
    {
        return $this->inner;
    }

    public function count()
    {
        return count($this->inner);
    }
}
