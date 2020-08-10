<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Communication;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Config\Upload;

class PushImportService
{
    /** @var Communication */
    private $communicationConfig;

    /** @var Upload */
    private $uploadConfig;

    public function __construct(Communication $communicationConfig, Upload $uploadConfig)
    {
        $this->communicationConfig = $communicationConfig;
        $this->uploadConfig        = $uploadConfig;
    }

    /**
     * @return bool
     *
     * @throws BadResponseException
     */
    public function execute(): bool
    {
        $types = $this->uploadConfig->getPushImportTypes();
        if (empty($types)) {
            return false;
        }

        if ($this->isRunning()) {
            throw new BadResponseException('Push import is currently running. Please make sure that import process is finished before starting new one.');
        }

        $client = $this->client();
        foreach ($types as $type) {
            $client->post($type . '?' . http_build_query(['channel' => $this->communicationConfig->getChannel(), 'quiet' => 'true']));
        }

        return true;
    }

    private function isRunning(): bool
    {
        $query    = http_build_query(['channel' => $this->communicationConfig->getChannel()]);
        $response = $this->client()->get('running?' . $query);

        return filter_var($response->getBody(), FILTER_VALIDATE_BOOLEAN);
    }

    private function getBaseEndpoint(): string
    {
        return $this->communicationConfig->getServerUrl() . '/rest/v3/import/';
    }

    private function client(): Client
    {
        return new Client([
            'headers'  => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => (string) $this->communicationConfig->getCredentials(),
            ],
            'base_uri' => $this->getBaseEndpoint(),
        ]);
    }
}
