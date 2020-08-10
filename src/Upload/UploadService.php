<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Upload;

use League\Flysystem\Adapter\Ftp as FtpAdapter;
use League\Flysystem\Filesystem;
use Omikron\FactFinder\Shopware6\Config\Upload;
use Omikron\FactFinder\Shopware6\Export\Stream\TmpFile;

class UploadService
{
    /** @var Upload */
    private $config;

    public function __construct(Upload $config)
    {
        $this->config = $config;
    }

    public function upload(callable $generate): void
    {
        $tmpFile = new TmpFile();
        $connection = $filesystem = new Filesystem(new FtpAdapter($this->config()));
        $generate($tmpFile);
        $connection->putStream($this->config->getUploadFileName(), $tmpFile());
    }

    private function config(): array
    {
        return [
            'host'     => $this->config->getHost(),
            'port'     => $this->config->getPort(),
            'username' => $this->config->getUserName(),
            'password' => $this->config->getPassword(),
            'ssl'      => true,
        ];
    }
}
