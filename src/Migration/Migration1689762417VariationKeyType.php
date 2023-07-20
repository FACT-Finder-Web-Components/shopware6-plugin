<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1689762417VariationKeyType extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1689762417;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
ALTER TABLE `factfinder_feed_preprocessor` MODIFY `variation_key` LONGTEXT;
SQL;

        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
