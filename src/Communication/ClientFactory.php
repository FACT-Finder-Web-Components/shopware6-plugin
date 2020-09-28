<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Communication;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Omikron\FactFinder\Shopware6\Config\Communication as CommunicationConfig;

class ClientFactory
{
    /** @var CommunicationConfig */
    private $config;

    public function __construct(CommunicationConfig $config)
    {
        $this->config = $config;
    }

    public function create(): ClientInterface
    {
        return new Client([
            'auth'     => $this->config->getCredentials(),
            'base_uri' => rtrim($this->config->getServerUrl(), '/') . '/rest/v3/',
            'headers'  => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }
}
