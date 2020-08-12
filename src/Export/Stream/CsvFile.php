<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Stream;

class CsvFile implements StreamInterface
{
    /** @var resource */
    private $fileResource;

    /** @var string */
    private $delimiter;

    /**
     * @param resource $fileResource
     * @param string   $delimiter
     */
    public function __construct($fileResource, string $delimiter = ';')
    {
        $this->fileResource = $fileResource;
        $this->delimiter    = $delimiter;
    }

    public function addEntity(array $entity): void
    {
        fputcsv($this->fileResource, $entity, $this->delimiter);
    }
}
