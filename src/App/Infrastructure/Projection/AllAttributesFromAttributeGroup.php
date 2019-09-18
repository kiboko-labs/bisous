<?php

namespace App\Infrastructure\Projection;

use App\Domain\Configuration\DTO\Attribute;
use App\Domain\Configuration\DTO\Label;
use App\Domain\Configuration\DTO\MetricAttribute;

class AllAttributesFromAttributeGroup implements \IteratorAggregate
{
    /** @var \PDO */
    private $connection;
    /** @var int */
    private $familyId;
    /** @var int */
    private $attributeGroupId;

    public function __construct(\PDO $connection, int $familyId, int $attributeGroupId)
    {
        $this->connection = $connection;
        $this->familyId = $familyId;
        $this->attributeGroupId = $attributeGroupId;
    }

    public function getIterator()
    {
        $statement = $this->connection->prepare(<<<SQL
SELECT
    attribute.attribute_id AS id,
    attribute.frontend_label AS label, 
    attribute.attribute_code AS code,
    type_mapping.akeneo_type,
    type_mapping.metric_family,
    type_mapping.default_metric_unit
FROM eav_entity_attribute AS entity_attribute
INNER JOIN eav_entity_type AS entity
    ON entity.entity_type_id = entity_attribute.entity_type_id
INNER JOIN eav_attribute_set AS family
    ON family.attribute_set_id = entity_attribute.attribute_set_id
INNER JOIN eav_attribute_group AS attribute_group
    ON attribute_group.attribute_group_id = entity_attribute.attribute_group_id
INNER JOIN eav_attribute AS attribute
    ON attribute.attribute_id = entity_attribute.attribute_id
LEFT JOIN (
    SELECT
           'pim_catalog_identifier' AS akeneo_type,
           NULL AS attribute_model,
           'catalog/product_attribute_backend_sku' AS backend_model,
           'static' AS backend_type,
           NULL AS frontend_model,
           'text' AS frontend_input,
           NULL AS source_model,
           NULL AS metric_family,
           NULL AS default_metric_unit
    UNION SELECT
           'pim_catalog_textarea' AS akeneo_type,
           NULL AS attribute_model,
           NULL AS backend_model,
           'text' AS backend_type,
           NULL AS frontend_model,
           'textarea' AS frontend_input,
           NULL AS source_model,
           NULL AS metric_family,
           NULL AS default_metric_unit
    UNION SELECT
           'pim_catalog_textarea' AS akeneo_type,
           NULL AS attribute_model,
           NULL AS backend_model,
           'varchar' AS backend_type,
           NULL AS frontend_model,
           'textarea' AS frontend_input,
           NULL AS source_model,
           NULL AS metric_family,
           NULL AS default_metric_unit
    UNION SELECT
           'pim_catalog_text' AS akeneo_type,
           NULL AS attribute_model,
           NULL AS backend_model,
           'varchar' AS backend_type,
           NULL AS frontend_model,
           'text' AS frontend_input,
           NULL AS source_model,
           NULL AS metric_family,
           NULL AS default_metric_unit
    UNION SELECT
           'pim_catalog_date' AS akeneo_type,
           NULL AS attribute_model,
           'catalog/product_attribute_backend_startdate' AS backend_model,
           'datetime' AS backend_type,
           'eav/entity_attribute_frontend_datetime' AS frontend_model,
           'date' AS frontend_input,
           NULL AS source_model,
           NULL AS metric_family,
           NULL AS default_metric_unit
    UNION SELECT
           'pim_catalog_date' AS akeneo_type,
           NULL AS attribute_model,
           'eav/entity_attribute_backend_datetime' AS backend_model,
           'datetime' AS backend_type,
           'eav/entity_attribute_frontend_datetime' AS frontend_model,
           'date' AS frontend_input,
           NULL AS source_model,
           NULL AS metric_family,
           NULL AS default_metric_unit
    UNION SELECT
           'pim_catalog_simpleselect' AS akeneo_type,
           NULL AS attribute_model,
           NULL AS backend_model,
           'int' AS backend_type,
           NULL AS frontend_model,
           'select' AS frontend_input,
           'catalog/product_status' AS source_model,
           NULL AS metric_family,
           NULL AS default_metric_unit
    UNION SELECT
           'pim_catalog_simpleselect' AS akeneo_type,
           NULL AS attribute_model,
           NULL AS backend_model,
           'int' AS backend_type,
           NULL AS frontend_model,
           'select' AS frontend_input,
           'catalog/product_visibility' AS source_model,
           NULL AS metric_family,
           NULL AS default_metric_unit
    UNION SELECT
           'pim_catalog_simpleselect' AS akeneo_type,
           NULL AS attribute_model,
           NULL AS backend_model,
           'int' AS backend_type,
           NULL AS frontend_model,
           'select' AS frontend_input,
           'eav/entity_attribute_source_table' AS source_model,
           NULL AS metric_family,
           NULL AS default_metric_unit
    UNION SELECT
           'pim_catalog_image' AS akeneo_type,
           NULL AS attribute_model,
           NULL AS backend_model,
           'varchar' AS backend_type,
           'catalog/product_attribute_frontend_image' AS frontend_model,
           'media_image' AS frontend_input,
           NULL AS source_model,
           NULL AS metric_family,
           NULL AS default_metric_unit
    UNION SELECT
           'pim_catalog_metric' AS akeneo_type,
           NULL AS attribute_model,
           NULL AS backend_model,
           'decimal' AS backend_type,
           NULL AS frontend_model,
           'weight' AS frontend_input,
           NULL AS source_model,
           'Weight' AS metric_family,
           'KILOGRAM' AS default_metric_unit
) AS type_mapping
    ON (type_mapping.attribute_model=attribute.attribute_model OR (type_mapping.attribute_model IS NULL AND attribute.attribute_model IS NULL))
    AND (type_mapping.backend_model=attribute.backend_model OR (type_mapping.backend_model IS NULL AND attribute.backend_model IS NULL))
    AND (type_mapping.backend_type=attribute.backend_type OR (type_mapping.backend_type IS NULL AND attribute.backend_type IS NULL))
    AND (type_mapping.frontend_model=attribute.frontend_model OR (type_mapping.frontend_model IS NULL AND attribute.frontend_model IS NULL))
    AND (type_mapping.frontend_input=attribute.frontend_input OR (type_mapping.frontend_input IS NULL AND attribute.frontend_input IS NULL))
    AND (type_mapping.source_model=attribute.source_model OR (type_mapping.source_model IS NULL AND attribute.source_model IS NULL))
WHERE entity.entity_type_code = 'catalog_product'
  AND entity_attribute.attribute_set_id = :familyId
  AND entity_attribute.attribute_group_id = :groupId
ORDER BY family.sort_order ASC, attribute_group.sort_order
SQL
        );

        $statement->execute([
            'familyId' => $this->familyId,
            'groupId' => $this->attributeGroupId,
        ]);

        foreach ($statement as $row) {
            if ($row['akeneo_type'] === 'pim_catalog_metric') {
                yield new MetricAttribute(
                    $row['code'],
                    new Label($row['label']),
                    'ad-hoc',
                    $row['metric_family'],
                    $row['default_metric_unit']
                );
            } else {
                yield new Attribute(
                    $row['code'],
                    new Label($row['label']),
                    'ad-hoc',
                    $row['akeneo_type'] ?? 'pim_catalog_text'
                );
            }
        }
    }
}