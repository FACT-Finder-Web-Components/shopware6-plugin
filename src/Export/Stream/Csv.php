<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Stream;

use SplFileObject as File;

class Csv implements StreamInterface
{
    /** @var File */
    private $handle;

    /** @var string */
    private $delimiter;

    public function __construct(File $handle, string $delimiter = ';')
    {
        $this->handle    = $handle;
        $this->delimiter = $delimiter;
    }

    public function addEntity(array $entity): void
    {
        $this->handle->fputcsv($entity, $this->delimiter);
    }
}
