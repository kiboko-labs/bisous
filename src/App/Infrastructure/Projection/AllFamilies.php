<?php

namespace App\Infrastructure\Projection;

use App\Domain\Configuration\DTO\Family;
use App\Domain\Configuration\DTO\Label;

class AllFamilies implements \IteratorAggregate
{
    /** @var \PDO */
    private $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getIterator()
    {
        $statement = $this->connection->query(<<<SQL
SELECT
    family.attribute_set_id AS id,
    family.attribute_set_name AS label, 
    LOWER(family.attribute_set_name) AS code
FROM eav_attribute_set AS family
INNER JOIN eav_entity_type AS entity
    ON entity.entity_type_id = family.entity_type_id
WHERE entity.entity_type_code = 'catalog_product'
ORDER BY family.sort_order ASC
SQL
        );

        foreach ($statement as $row) {
            yield new Family(
                $row['code'],
                new Label($row['label']),
                ...new AllAttributeGroupsFromFamily($this->connection, $row['id'])
            );
        }
    }
}