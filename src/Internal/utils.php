<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Internal\Utils;

use Closure;

function safeGetByName(?array $collection): Closure
{
    return fn (string $name) => is_array($collection) && isset($collection[$name]) && $collection[$name];
}

function flatMap(callable $fnc, array $arr, array ...$arrays): array
{
    return array_merge([], ...array_map($fnc, $arr, ...$arrays));
}
