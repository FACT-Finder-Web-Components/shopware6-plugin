<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Upload;

use League\Flysystem\FilesystemException;
use Omikron\FactFinder\Shopware6\Config\FtpConfig;
use Shopware\Core\Framework\Adapter\Filesystem\FilesystemFactory;

class UploadService
{
    public function __construct(
        private readonly FtpConfig $config,
        private readonly FilesystemFactory $filesystemFactory
    ) {
    }

    /**
     * @param $fileHandle
     *
     * @throws FilesystemException
     */
    public function upload($fileHandle): void
    {
        $connection     = $this->filesystemFactory->factory($this->config());
        $connection->writeStream(basename(stream_get_meta_data($fileHandle)['uri']), $fileHandle);
    }

    /**
     * @throws FilesystemException
     */
    public function testConnection(): void
    {
        $connection = $this->filesystemFactory->factory($this->config());
        $connection->write('test-connection.txt', 'S/FTP test connection');
        $connection->delete('test-connection.txt');
    }

    private function config(): array
    {
        return [
            'type'   => $this->config->getProtocol(),
            'config' => array_filter([
                'host'       => $this->config->getHost(),
                'port'       => $this->config->getPort(),
                'username'   => $this->config->getUserName(),
                'password'   => $this->config->getPassword(),
                'ssl'        => true,
                'privateKey' => $this->config->getPrivateKeyFile(),
                'passphrase' => $this->config->getKeyPassphrase(),
                'root'       => $this->config->getRoot(),
            ]),
        ];
    }
}
