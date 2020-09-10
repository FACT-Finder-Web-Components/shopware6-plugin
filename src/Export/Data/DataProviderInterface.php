<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data;

interface DataProviderInterface
{
    /**
     * @return ExportEntityInterface[]
     */
    public function getEntities(): iterable;
}
