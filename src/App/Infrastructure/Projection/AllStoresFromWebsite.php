<?php

namespace App\Infrastructure\Projection;

use App\Domain\Configuration\DTO\Label;
use App\Domain\Configuration\DTO\Localised;
use App\Domain\Configuration\DTO\Store;

class AllStoresFromWebsite implements \IteratorAggregate
{
    /** @var \PDO */
    private $connection;
    /** @var int */
    private $websiteId;

    public function __construct(\PDO $connection, int $websiteId)
    {
        $this->connection = $connection;
        $this->websiteId = $websiteId;
    }

    public function getIterator()
    {
        $statement = $this->connection->prepare(<<<SQL
SELECT 
   store.store_id AS id, 
   store.code AS code,
   store.name AS label, 
   (store.store_id = store_group.default_store_id) As is_default
FROM core_store AS store
INNER JOIN core_store_group AS store_group
    ON store_group.group_id = store.group_id
WHERE store.website_id = :websiteId
ORDER BY store.store_id = store_group.default_store_id DESC
SQL
        );

        $statement->execute([
            'websiteId' => $this->websiteId,
        ]);

        foreach ($statement as $row) {
            $locale = (new OneLocaleFromStore($this->connection, $this->websiteId, $row['id']))->get();

            yield new Store(
                $row['code'],
                new Label($row['label'], new Localised($locale, $row['label'])),
                $row['id'],
                $locale
            );
        }
    }
}