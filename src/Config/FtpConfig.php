<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

class FtpConfig extends BaseConfig
{
    public function getHost(): string
    {
        return (string) $this->config('ftpHost');
    }

    public function getPort(): string
    {
        return (string) $this->config('ftpPort') ?: '21';
    }

    public function getUserName(): string
    {
        return (string) $this->config('ftpUsername');
    }

    public function getPassword(): string
    {
        return (string) $this->config('ftpPassword');
    }

    public function getUploadFileName(?string $salesChannelId = null): string
    {
        return sprintf('export.%s.csv', $this->config('channel', $salesChannelId));
    }

    public function getProtocol(): string
    {
        return (string) $this->config('protocol');
    }

    public function getPrivateKeyFile(): string
    {
        return (string) $this->config('privateKey');
    }

    public function getKeyPassphrase(): string
    {
        return (string) $this->config('keyPassphrase');
    }

    public function getPushImportTypes(): array
    {
        return (array) $this->config('pushImport');
    }
}
