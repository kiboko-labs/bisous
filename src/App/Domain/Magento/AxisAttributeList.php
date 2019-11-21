<?php declare(strict_types=1);

namespace App\Domain\Magento;

use Traversable;

class AxisAttributeList implements \IteratorAggregate
{
    /** @var AttributeRenderer[] */
    public $attributes;

    public function __construct(AttributeRenderer ...$renderers)
    {
        $this->attributes = $renderers;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->attributes);
    }
}