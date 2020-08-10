<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Stream;

class TmpFile implements StreamInterface
{
    /** @var resource */
    private $fileResource;

    public function addEntity(array $entity): void
    {
        $this->fileResource = $this->fileResource ?? tmpfile();
        fputcsv($this->fileResource, $entity, ';');
    }

    public function __invoke()
    {
        return $this->fileResource;
    }
}
