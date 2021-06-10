<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Upload;

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

    public function upload($fileHandle): void
    {
        $connection     = $this->filesystemFactory->factory($this->config());
        $salesChannelId = $this->salesChannelService->getSalesChannelContext()->getSalesChannel()->getId();
        $connection->putStream($this->config->getUploadFileName($salesChannelId), $fileHandle);
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
            ],
        ];
    }
}
