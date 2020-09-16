<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Communication;

use GuzzleHttp\Client;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Config\FtpConfig;
use Omikron\FactFinder\Shopware6\Exception\ImportRunningException;

class PushImportService
{
    /** @var Communication */
    private $communicationConfig;

    /** @var FtpConfig */
    private $uploadConfig;

    public function __construct(Communication $communicationConfig, FtpConfig $uploadConfig)
    {
        $this->communicationConfig = $communicationConfig;
        $this->uploadConfig        = $uploadConfig;
    }

    /**
     * @return bool
     *
     * @throws ImportRunningException
     */
    public function execute(): bool
    {
        $this->checkNotRunning();

        $client = $this->client();
        $query  = http_build_query(['channel' => $this->communicationConfig->getChannel(), 'quiet' => 'true']);
        foreach ($this->uploadConfig->getPushImportTypes() as $type) {
            $client->post($type . '?' . $query);
        }

        return true;
    }

    /**
     * @throws ImportRunningException
     */
    private function checkNotRunning(): void
    {
        $query    = http_build_query(['channel' => $this->communicationConfig->getChannel()]);
        $response = $this->client()->get('running?' . $query);

        if (filter_var($response->getBody(), FILTER_VALIDATE_BOOLEAN)) {
            throw new ImportRunningException();
        }
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
