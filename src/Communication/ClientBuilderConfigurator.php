<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Communication;

use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Credentials;
use Omikron\FactFinder\Shopware6\Config\Communication as CommunicationConfig;
use Psr\Log\LoggerInterface;

readonly class ClientBuilderConfigurator
{
    public function __construct(
        private CommunicationConfig $config,
        private LoggerInterface $factfinderLogger,
    ) {
    }

    public function configure(ClientBuilder $clientBuilder): void
    {
        $clientBuilder->withCredentials(new Credentials(...$this->config->getCredentials()));

        try {
            if ($this->config->getServerUrl() !== '') {
                $clientBuilder->withServerUrl($this->config->getServerUrl());
            }
        } catch (\InvalidArgumentException $e) {
            $this->factfinderLogger->error($e->getMessage());
        }
    }
}
