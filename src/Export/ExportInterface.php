<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * @api
 */
interface ExportInterface
{
    public function getByContext(SalesChannelContext $context, int $batchSize = 100): iterable;

    public function getCoveredEntityType(): string;

    public function getProducedExportEntityType(): string;
}
