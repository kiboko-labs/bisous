<?php

namespace App\Domain\Configuration\DTO;

class MetricAttribute extends Attribute
{
    /** @var string */
    public $metricFamily;
    /** @var string */
    public $defaultMetric;

    public function __construct(
        string $code,
        Label $label,
        string $strategy,
        string $metricFamily,
        string $defaultMetric
    ) {
        parent::__construct($code, $label, $strategy, 'pim_catalog_metric');
        $this->metricFamily = $metricFamily;
        $this->defaultMetric = $defaultMetric;
    }
}