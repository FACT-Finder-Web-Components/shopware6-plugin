<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use Shopware\Core\Framework\Adapter\Filesystem\Adapter\AdapterFactoryInterface;

class SftpAdapterFactory implements AdapterFactoryInterface
{
    public function create(array $config): FilesystemAdapter
    {
        return new SftpAdapter(
            new SftpConnectionProvider(
                $config['host'],
                $config['username'],
                $config['password'] ?? null,
                $config['privateKey'] ?? null,
                $config['passphrase'] ?? null,
                (int) $config['port'] ?? 22,
            ),
            $config['root']
        );
    }

    public function getType(): string
    {
        return 'sftp';
    }
}
