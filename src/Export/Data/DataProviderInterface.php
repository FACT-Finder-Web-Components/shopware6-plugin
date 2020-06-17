<?php

namespace Omikron\FactFinder\Shopware6\Export\Data;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;

interface DataProviderInterface
{
    /**
     * @return ExportEntityInterface[]
     */
    public function getEntities(): iterable;
}
