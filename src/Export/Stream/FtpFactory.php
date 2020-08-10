<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Stream;

use League\Flysystem\Adapter\Ftp as FtpAdapter;
use League\Flysystem\Filesystem;
use Omikron\FactFinder\Shopware6\Config\Upload;

class FtpFactory
{
    /** @var Upload */
    private $config;

    public function __construct(Upload $config)
    {
        $this->config = $config;
    }

    public function create(): Ftp
    {
        $filesystem = new Filesystem(new FtpAdapter($this->config()));
        return new Ftp($filesystem, $this->config->getUploadFileName());
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
