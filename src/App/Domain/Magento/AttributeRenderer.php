<?php

namespace App\Domain\Magento;

use Twig\Environment;
use Twig\TemplateWrapper;

interface AttributeRenderer
{
    public function __toString();
    public function __invoke(TemplateWrapper $template): string;
    public function template(Environment $twig): TemplateWrapper;
    public function attribute(): Attribute;
    public function fields(): iterable;
    public function isAxis(): bool;
    public function isScoped(): bool;
    public function isLocalized(): bool;
}