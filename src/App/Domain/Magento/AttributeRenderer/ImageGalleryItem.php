<?php

namespace App\Domain\Magento\AttributeRenderer;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\FieldResolver;
use App\Domain\Magento\VariantAxis;
use Twig\Environment;
use Twig\TemplateWrapper;

class ImageGalleryItem implements AttributeRenderer
{
    use ScopingAwareTrait;
    use LocalizationAwareTrait;

    /** @var Attribute */
    private $attribute;
    /** @var FieldResolver */
    private $fieldResolver;
    /** @var int */
    private $position;

    public function __construct(
        Attribute $attribute,
        FieldResolver $fieldResolver,
        int $position
    ) {
        $this->attribute = $attribute;

        if ($fieldResolver instanceof VariantAxis) {
            throw new \TypeError('Could not accept a VariantAxis renderer in am Image attribute.');
        }

        $this->fieldResolver = $fieldResolver;
        $this->position = $position;
    }

    public function __toString()
    {
        return 'image-gallery-item';
    }

    public function __invoke(TemplateWrapper $template): string
    {
        if ($this->attribute instanceof Attribute\ExNihilo) {
            return '';
        }

        return $template->render([
            'attribute' => $this->attribute,
            'fields' => $this->fields(),
            'position' => $this->position
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