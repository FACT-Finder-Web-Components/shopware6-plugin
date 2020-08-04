<?php

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
