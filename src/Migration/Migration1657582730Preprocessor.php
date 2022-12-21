<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1657582730Preprocessor extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1657582730;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
CREATE TABLE IF NOT EXISTS `factfinder_feed_preprocessor` (
    `id`                           BINARY(16)               NOT NULL,
    `language_id`                  BINARY(16)               NOT NULL,
    `product_number`               VARCHAR(255)             NOT NULL,
    `parent_product_number`        VARCHAR(255),
    `variation_key`                VARCHAR(255),
    `filter_attributes`            LONGTEXT                 NOT NULL,
    `custom_fields`                LONGTEXT                 NOT NULL,
    `additional_cache`             JSON,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    PRIMARY KEY (id)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        $query = <<<SQL
DROP TABLE IF EXISTS `factfinder_feed_preprocessor`
SQL;
        $connection->executeStatement($query);
    }
}
