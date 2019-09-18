<?php

namespace App\Infrastructure\Projection;

use App\Domain\Configuration\DTO\AttributeGroup;
use App\Domain\Configuration\DTO\Label;

class AllAttributeGroupsFromFamily implements \IteratorAggregate
{
    /** @var \PDO */
    private $connection;
    /** @var int */
    private $familyId;

    public function __construct(\PDO $connection, int $familyId)
    {
        $this->connection = $connection;
        $this->familyId = $familyId;
    }

    public function getIterator()
    {
        $statement = $this->connection->prepare(<<<SQL
SELECT
    attribute_group.attribute_group_id AS id,
    attribute_group.attribute_group_name AS label, 
    LOWER(attribute_group.attribute_group_name) AS code
FROM eav_attribute_group AS attribute_group
INNER JOIN eav_attribute_set AS family
    ON family.attribute_set_id = attribute_group.attribute_set_id
INNER JOIN eav_entity_type AS entity
    ON entity.entity_type_id = family.entity_type_id
WHERE entity.entity_type_code = 'catalog_product'
  AND family.attribute_set_id = :familyId
ORDER BY family.sort_order ASC, attribute_group.sort_order
SQL
        );

        $statement->execute([
            'familyId' => $this->familyId,
        ]);

        foreach ($statement as $row) {
            yield new AttributeGroup(
                $row['code'],
                new Label($row['label']),
                ...new AllAttributesFromAttributeGroup($this->connection, $this->familyId, $row['id'])
            );
        }
    }
}