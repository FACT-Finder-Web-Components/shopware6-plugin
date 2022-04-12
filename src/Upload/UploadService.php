<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Upload;

use Exception;
use Omikron\FactFinder\Shopware6\Config\FtpConfig;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Framework\Adapter\Filesystem\FilesystemFactory;

class UploadService
{
    private FtpConfig $config;
    private FilesystemFactory $filesystemFactory;
    private SalesChannelService $salesChannelService;

    public function __construct(
        FtpConfig $config,
        FilesystemFactory $filesystemFactory,
        SalesChannelService $salesChannelService
    ) {
        $this->config              = $config;
        $this->filesystemFactory   = $filesystemFactory;
        $this->salesChannelService = $salesChannelService;
    }

    /**
     * @param $fileHandle
     *
     * @throws IOException when failed to upload file
     */
    public function upload($fileHandle): void
    {
        $connection     = $this->filesystemFactory->factory($this->config());
        /* @todo v4: inject naming strategy to file. Do not rely on file metadata */
        if (!$connection->putStream(basename(stream_get_meta_data($fileHandle)['uri']), $fileHandle)) {
            throw new Exception('Failed to upload file');
        }
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
