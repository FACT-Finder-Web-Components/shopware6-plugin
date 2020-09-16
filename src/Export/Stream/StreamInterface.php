<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Stream;

/**
 * @api
 */
interface StreamInterface
{
    /**
     * @param array $entity
     */
    public function addEntity(array $entity): void;
}
