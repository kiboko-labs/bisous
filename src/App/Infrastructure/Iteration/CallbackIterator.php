<?php

namespace App\Infrastructure\Iteration;

class CallbackIterator extends AbstractCallbackIterator
{
    public function current()
    {
        return ($this->callback)($this->inner->current());
    }
}
