<?php

namespace Omikron\FactFinder\Shopware6\Export\Filter;

/**
 * @api
 */
interface FilterInterface
{
    public function filterValue(string $value): string;
}
