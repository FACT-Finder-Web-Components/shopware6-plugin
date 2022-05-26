<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

class Communication extends BaseConfig
{
    public function getServerUrl(): string
    {
        return trim((string) $this->config('serverUrl'));
    }

    public function getChannel(?string $salesChannelId = null): string
    {
        return (string) $this->config('channel', $salesChannelId);
    }

    public function getCredentials(): array
    {
        return [
            (string) $this->config('username'),
            (string) $this->config('password'),
        ];
    }

    public function getFieldRoles(?string $salesChannelId): array
    {
        return (array) $this->config('fieldRoles', $salesChannelId);
    }
}
