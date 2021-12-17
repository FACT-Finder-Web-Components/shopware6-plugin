<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Internal\Utils;

use Closure;

function safeGetByName(?array $collection): Closure
{
    return fn (string $name): ?string => isset($collection[$name]) ? $collection[$name] : null;
}

function flatMap(callable $fnc, array $arr, array ...$arrays): array
{
    return array_merge([], ...array_map($fnc, $arr, ...$arrays));
}

function first(?array $collection, $default = null)
{
    return empty($collection) ? $default : reset($collection);
}
