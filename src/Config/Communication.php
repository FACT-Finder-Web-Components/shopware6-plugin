<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

class Communication extends BaseConfig
{
    public function getServerUrl(): string
    {
        return trim((string) $this->config('serverUrl'));
    }

    public function getChannel(): string
    {
        return (string) $this->config('channel');
    }

    public function getCredentials(): array
    {
        return [$this->config('username'), $this->config('password')];
    }
}
