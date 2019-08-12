<?php

namespace Kiboko\Bridge\Akeneo\Magento\AttributeRenderer;

use Kiboko\Bridge\Akeneo\Magento\Attribute;
use Kiboko\Bridge\Akeneo\Magento\AttributeRenderer;
use Kiboko\Bridge\Akeneo\Magento\FieldResolver;
use Kiboko\Bridge\Akeneo\Magento\VariantAxis;
use Twig\TemplateWrapper;

class Status implements AttributeRenderer
{
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
            throw new \TypeError('Could not accept a VariantAxis renderer in a Status attrinute.');
        }

        $this->fieldResolver = $fieldResolver;
    }

    public function __toString()
    {
        return 'status';
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

    public function template(): string
    {
        return $this->fieldResolver->template($this);
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