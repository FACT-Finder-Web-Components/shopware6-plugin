<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use League\Flysystem\Adapter\Ftp as FtpAdapter;
use League\Flysystem\AdapterInterface;
use Shopware\Core\Framework\Adapter\Filesystem\Adapter\AdapterFactoryInterface;

class FtpAdapterFactory implements AdapterFactoryInterface
{
    public function create(array $config): AdapterInterface
    {
        return new FtpAdapter($config);
    }

    public function getType(): string
    {
        return 'ftp';
    }
}
