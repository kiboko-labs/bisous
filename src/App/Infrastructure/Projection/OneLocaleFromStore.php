<?php

namespace App\Infrastructure\Projection;

use App\Domain\Configuration\DTO\Locale;

class OneLocaleFromStore
{
    /** @var \PDO */
    private $connection;
    /** @var int */
    private $websiteId;
    /** @var int */
    private $storeId;

    public function __construct(\PDO $connection, int $websiteId, int $storeId)
    {
        $this->connection = $connection;
        $this->websiteId = $websiteId;
        $this->storeId = $storeId;
    }

    public function get(): Locale
    {
        $statement = $this->connection->prepare(<<<SQL
SELECT COALESCE(store.value, website.value, global.value) AS code
FROM core_config_data AS global
LEFT JOIN core_config_data AS website
    ON website.path = global.path 
    AND website.scope = 'websites'
    AND website.scope_id = :websiteId
LEFT JOIN core_config_data AS store
    ON store.path = global.path
    AND store.scope = 'stores'
    AND store.scope_id = :storeId
WHERE global.path = 'general/locale/code'
  AND global.scope = 'default'
  AND global.scope_id = 0
SQL
        );

        $statement->execute([
            'websiteId' => $this->websiteId,
            'storeId' => $this->storeId,
        ]);

        return new Locale($statement->fetchColumn());
    }
}