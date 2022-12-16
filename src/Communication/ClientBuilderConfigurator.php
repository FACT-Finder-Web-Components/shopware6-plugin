<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Communication;

use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Credentials;
use Omikron\FactFinder\Shopware6\Config\Communication as CommunicationConfig;

class ClientBuilderConfigurator
{
    private CommunicationConfig $config;

    public function __construct(CommunicationConfig $config)
    {
        $this->config = $config;
    }

    public function configure(ClientBuilder $clientBuilder): void
    {
        $clientBuilder->withCredentials(new Credentials(...$this->config->getCredentials()));

        if ($this->config->getServerUrl() !== '') {
            $clientBuilder->withServerUrl($this->config->getServerUrl());
        }
    }
}
