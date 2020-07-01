<?php

namespace Omikron\FactFinder\Shopware6\Export\Data;

interface DataProviderInterface
{
    /**
     * @return ExportEntityInterface[]
     */
    public function getEntities(): iterable;
}
