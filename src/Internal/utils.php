<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Internal\Utils;

/**
 * @param array|null $collection
 * @param string     $name
 *
 * @return mixed|null
 */
function safeGetByName(?array $collection, string $name)
{
    return $collection[$name] ?? null;
}

function flatMap(callable $fnc, array $arr, array ...$arrays): array
{
    return array_merge([], ...array_map($fnc, $arr, ...$arrays));
}

/**
 * @param array|null $collection
 * @param null       $default
 *
 * @return false|mixed|null
 */
function first(?array $collection, $default = null)
{
    return empty($collection) ? $default : reset($collection);
}
