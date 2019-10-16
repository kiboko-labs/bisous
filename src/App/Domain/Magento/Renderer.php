<?php

namespace App\Domain\Magento;

use Twig\Environment;

class Renderer
{
    /** @var string */
    private $initialization;
    /** @var string */
    private $finalization;
    /** @var string[] */
    private $types;
    /** @var AttributeRenderer */
    private $attributes;

    public function __construct(
        string $initialization,
        string $finalization,
        array $types,
        AttributeRenderer ...$attributes
    ) {
        $this->initialization = $initialization;
        $this->finalization = $finalization;
        $this->types = $types;
        $this->attributes = $attributes;
    }

    public function __invoke($stream, Environment $twig)
    {
        if (!is_resource($stream) || !get_resource_type($stream) === 'stream') {
            throw new \TypeError(strtr('Expected a stream resource as first argument, got %actual%.', [
                '%actual%' => is_resource($stream) ? sprintf('resource(%s)', get_resource_type($stream)) :
                    (is_object($stream) ? sprintf('object(%s)', get_class($stream)) : gettype($stream))
            ]));
        }

        $template = $twig->load($this->initialization);
        fwrite($stream, $template->render([
            'attributes' => array_map(function (AttributeRenderer $renderer) {
                return $renderer->attribute();
            }, $this->attributes),
            'fields' => array_merge([], ...array_map(function (AttributeRenderer $renderer) {
                return $renderer->fields();
            }, $this->attributes)),
            'axises' => array_merge([], ...array_map(function (AttributeRenderer $renderer) {
                return $renderer->fields();;
            }, array_filter($this->attributes, function (AttributeRenderer $renderer) {
                return $renderer->isAxis();
            }))),
        ]) . PHP_EOL);

        /** @var AttributeRenderer $attribute */
        foreach ($this->attributes as $attribute) {
            $template = $twig->load($attribute->template());

            fwrite($stream, $attribute($template) . PHP_EOL);
        }

//        $template = $twig->load($this->finalization);
//        fwrite($stream, $template->render([
//            'types' => $this->types,
//        ]) . PHP_EOL);
    }
}