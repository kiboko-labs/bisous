<?php

namespace Kiboko\Bridge\Akeneo\Magento;

use Twig\TemplateWrapper;

interface AttributeRenderer
{
    public function __toString();
    public function __invoke(TemplateWrapper $template): string;
    public function template(): string;
    public function attribute(): Attribute;
    public function fields(): iterable;
    public function isAxis(): bool;
}