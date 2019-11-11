<?php

namespace App\Domain\Magento\AttributeRenderer;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\FieldResolver;
use App\Domain\Magento\VariantAxis;
use Twig\Environment;
use Twig\TemplateWrapper;

class Metric implements AttributeRenderer
{
    use ScopingAwareTrait;
    use LocalizationAwareTrait;

    /** @var Attribute */
    private $attribute;
    /** @var FieldResolver */
    private $fieldResolver;

    public function __construct(
        Attribute $attribute,
        FieldResolver $fieldResolver
    ) {
        $this->attribute = $attribute;

        if ($fieldResolver instanceof VariantAxis) {
            throw new \TypeError('Could not accept a VariantAxis renderer in a Metric attribute.');
        }

        $this->fieldResolver = $fieldResolver;
    }

    public function __toString()
    {
        return 'metric';
    }

    public function __invoke(TemplateWrapper $template): string
    {
        if ($this->attribute instanceof Attribute\ExNihilo) {
            return '';
        }

        return $template->render([
            'attribute' => $this->attribute,
            'fields' => $this->fields(),
        ]);
    }

    public function template(Environment $twig): TemplateWrapper
    {
        return $twig->load($this->fieldResolver->template($this));
    }

    public function attribute(): Attribute
    {
        return $this->attribute;
    }

    public function fields(): iterable
    {
        return $this->fieldResolver->fields($this->attribute);
    }

    public function isAxis(): bool
    {
        return false;
    }
}