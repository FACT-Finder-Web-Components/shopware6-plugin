<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use Shopware\Core\Framework\Adapter\Filesystem\Adapter\AdapterFactoryInterface;

class FtpAdapterFactory implements AdapterFactoryInterface
{
    public function create(array $config): FilesystemAdapter
    {
        return new FtpAdapter(new FtpConnectionOptions(
            $config['host'],
            $config['root'],
            $config['username'],
            $config['password'] ?? null,
            (int) $config['port'] ?? 21,
        ));
    }

    public function getType(): string
    {
        return 'ftp';
    }
}
