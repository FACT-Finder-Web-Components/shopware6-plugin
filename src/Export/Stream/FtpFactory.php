<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Stream;

use League\Flysystem\Adapter\Ftp as FtpAdapter;
use League\Flysystem\Filesystem;
use Omikron\FactFinder\Shopware6\Export\Upload\Config;

class FtpFactory
{
    /** @var Config */
    private $ftpConfig;

    public function __construct(Config $configuration, bool $graceful = true)
    {
        $this->ftpConfig = $configuration;
        $this->graceful  = $graceful;
    }

    public function create()
    {
        $filesystem = new Filesystem(new FtpAdapter($this->config()));
        $fileName =  $this->ftpConfig->getUploadFileName();
        if (!$this->graceful && $filesystem->has($fileName)) {
            throw new FileExistsException($fileName);
        }

        return new Ftp($filesystem, $fileName);
    }

    private function config()
    {
        return [
            'host'     => $this->ftpConfig->getHost(),
            'port'     => $this->ftpConfig->getPort(),
            'username' => $this->ftpConfig->getUserName(),
            'password' => $this->ftpConfig->getPassword(),
            'ssl'      => true,
        ];
    }
}
