<?php

namespace Omikron\FactFinder\Shopware6\Export\Data;

/**
 * @api
 */
interface ExportEntityInterface
{
    /**
     * Get entity ID
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Convert entity data to associative array
     *
     * @return array
     */
    public function toArray(): array;
}
