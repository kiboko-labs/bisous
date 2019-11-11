<?php

namespace App\Domain\Magento\AttributeRenderer;

use App\Domain\Magento\Attribute;
use App\Domain\Magento\AttributeRenderer;
use App\Domain\Magento\FieldResolver;
use App\Domain\Magento\VariantAxis;
use Twig\Environment;
use Twig\Template;
use Twig\TemplateWrapper;

class Identifier implements AttributeRenderer
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

        if (!$attribute instanceof Attribute\AdHoc) {
            throw new \TypeError('The identifier attribute must be be ad-hoc.');
        }
        if ($fieldResolver instanceof VariantAxis) {
            throw new \TypeError('Could not accept a VariantAxis renderer in an Identifier attribute.');
        }

        $this->fieldResolver = $fieldResolver;
    }

    public function __toString()
    {
        return 'identifier';
    }

    public function __invoke(TemplateWrapper $template): string
    {
        return '';
    }

    public function template(Environment $twig): TemplateWrapper
    {
        return new TemplateWrapper($twig, new class($twig) extends Template {
            public function getTemplateName(){return '<none>';}
            public function getDebugInfo(){return [];}
            public function doDisplay(array $context, array $blocks = []){return '';}
        });
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