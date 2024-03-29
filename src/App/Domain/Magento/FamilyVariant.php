<?php

namespace App\Domain\Magento;

class FamilyVariant
{
    /** @var Family */
    public $family;
    /** @var string */
    public $code;
    /** @var string */
    public $skuTemplate;
    /** @var FamilyVariantAxis[] */
    private $axises;

    public function __construct(Family $family, string $code, ?string $skuTemplate, FamilyVariantAxis ...$axises)
    {
        if (count($axises) > 2) {
            throw new \OverflowException('Could not initialise a family variant with more than 2 axis levels.');
        }

        $this->family = $family;
        $this->code = $code;
        $this->skuTemplate = $skuTemplate;
        $this->axises = $axises;
    }

    public function __toString()
    {
        return $this->code;
    }

    public function axis(int $level): FamilyVariantAxis
    {
        if ($level > count($this->axises)) {
            throw new \OutOfBoundsException('You requested an axis that does not exist.');
        }

        return $this->axises[$level - 1];
    }

    public function all(): \Iterator
    {
        yield from $this->axises;
    }

    public function isTwoLevels(): bool
    {
        return count($this->axises) === 2;
    }
}