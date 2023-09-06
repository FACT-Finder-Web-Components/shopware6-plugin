<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Filter;

/**
 * @api
 */
interface FilterInterface
{
    public function filterValue(?string $value): string;
}
