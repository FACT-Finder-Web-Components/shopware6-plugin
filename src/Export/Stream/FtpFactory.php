<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Stream;

use League\Flysystem\Adapter\Ftp as FtpAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FileExistsException;
use Omikron\FactFinder\Shopware6\Export\Upload\Config;

class FtpFactory
{
    /** @var Config */
    private $ftpConfig;

    /** @var bool */
    private $overrideExistingFile;

    public function __construct(Config $configuration, bool $overrideExistingFile)
    {
        $this->ftpConfig             = $configuration;
        $this->overrideExistingFile  = $overrideExistingFile;
    }

    public function create(): Ftp
    {
        $filesystem = new Filesystem(new FtpAdapter($this->config()));
        $fileName =  $this->ftpConfig->getUploadFileName();
        if (!$this->overrideExistingFile && $filesystem->has($fileName)) {
            throw new FileExistsException($fileName);
        }

        return new Ftp($filesystem, $fileName);
    }

    private function config(): array
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
