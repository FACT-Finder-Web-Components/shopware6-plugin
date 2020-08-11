<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Upload;

use Omikron\FactFinder\Shopware6\Config\FtpConfig;
use Shopware\Core\Framework\Adapter\Filesystem\FilesystemFactory;

class UploadService
{
    /** @var FtpConfig */
    private $config;

    /** @var FilesystemFactory */
    private $filesystemFactory;

    public function __construct(FtpConfig $config, FilesystemFactory $filesystemFactory)
    {
        $this->config            = $config;
        $this->filesystemFactory = $filesystemFactory;
    }

    public function upload($fileHandle): void
    {
        $connection = $this->filesystemFactory->factory($this->config());
        $connection->putStream($this->config->getUploadFileName(), $fileHandle);
    }

    private function config(): array
    {
        return [
            'type'   => 'ftp',
            'config' => [
                'host'     => $this->config->getHost(),
                'port'     => $this->config->getPort(),
                'username' => $this->config->getUserName(),
                'password' => $this->config->getPassword(),
                'ssl'      => true,
            ]];
    }
}
