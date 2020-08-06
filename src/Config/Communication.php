<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

use Omikron\FactFinder\Shopware6\Communication\Credentials;

class Communication extends BaseConfig
{
    public function getServerUrl(): string
    {
        return (string) $this->config('serverUrl');
    }

    public function getChannel(): string
    {
        return (string) $this->config('channel');
    }

    public function getCredentials(): Credentials
    {
        return new Credentials((string) $this->config('username'), (string) $this->config('password'));
    }
}
