<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Sftp\SftpAdapter;
use Shopware\Core\Framework\Adapter\Filesystem\Adapter\AdapterFactoryInterface;

class SftpAdapterFactory implements AdapterFactoryInterface
{
    public function create(array $config): AdapterInterface
    {
        return new SftpAdapter($config);
    }

    public function getType(): string
    {
        return 'sftp';
    }
}
