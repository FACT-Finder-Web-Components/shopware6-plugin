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

    /** @var Client */
    private $client;

    public function __construct(Client $client, Communication $communicationConfig, FtpConfig $uploadConfig)
    {
        $this->communicationConfig = $communicationConfig;
        $this->uploadConfig        = $uploadConfig;
        $this->client              = $client;
    }

    /**
     * @return bool
     *
     * @throws ImportRunningException
     */
    public function execute(): bool
    {
        $this->checkNotRunning();

        $query = ['channel' => $this->communicationConfig->getChannel(), 'quiet' => 'true'];
        foreach ($this->uploadConfig->getPushImportTypes() as $type) {
            $this->client->post("import/{$type}", ['form_params' => $query]);
        }

        return true;
    }

    /**
     * @throws ImportRunningException
     */
    private function checkNotRunning(): void
    {
        $query    = http_build_query(['channel' => $this->communicationConfig->getChannel()]);
        $response = $this->client->get('import/running?' . $query);

        if (filter_var((string) $response->getBody(), FILTER_VALIDATE_BOOLEAN)) {
            throw new ImportRunningException();
        }
    }
}
