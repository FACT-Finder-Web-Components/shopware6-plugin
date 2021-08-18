<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

interface ExportInterface
{
    public function getByContext(SalesChannelContext $context, int $batchSize = 100): iterable;

    public function getCriteria(int $batchSize): Criteria;

    public function getCoveredEntityType(): string;

    public function getProducedExportEntityType(): string;
}
