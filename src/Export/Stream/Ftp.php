<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Stream;

use League\Flysystem\FilesystemInterface;

class Ftp implements StreamInterface
{
    /** @var FilesystemInterface */
    private $connection;

    /** @var string */
    private $fileName;

    /** @var resource */
    private $fileResource;

    public function __construct(FilesystemInterface $connection, string $fileName)
    {
        $this->connection = $connection;
        $this->fileName   = $fileName;
    }

    public function addEntity(array $entity): void
    {
        $this->fileResource = $this->fileResource ?? tmpfile();
        fputcsv($this->fileResource, $entity, ';');
        $this->connection->putStream($this->fileName, $this->fileResource);
    }
}
