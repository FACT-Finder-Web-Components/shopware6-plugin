<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr;

use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientException;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Psr\Http\Message\ResponseInterface;

class SearchAdapter
{
    private ClientBuilder $clientBuilder;
    private Communication $config;
    private PriceFormatter $priceFormatter;

    public function __construct(
        ClientBuilder $clientBuilder,
        Communication $config,
        PriceFormatter $priceFormatter,
    ) {
        $this->clientBuilder  = $clientBuilder;
        $this->config         = $config;
        $this->priceFormatter = $priceFormatter;
    }

    public function search(
        string $paramString,
        bool $navigationRequest,
        string $salesChannelId,
    ): array {
        $client = $this->clientBuilder
            ->withServerUrl($this->config->getServerUrl())
            ->withApiKey($this->config->getApiKey())
            ->withVersion($this->config->getVersion())
            ->build();

        $endpoint = $this->createEndpoint($paramString, $navigationRequest, $salesChannelId);
        $response = $client->request('GET', $endpoint);

        if (!$response) {
            throw new ClientException('The response was empty');
        }

        return $this->priceFormatter->format($this->searchResult($response));
    }

    private function searchResult(ResponseInterface $response): array
    {
        return json_decode((string) $response->getBody(), true);
    }

    private function createEndpoint(string $paramString, bool $navigationRequest, string $salesChannelId)
    {
        $apiVersion = $this->config->getApiVersion();
        $channel    = $this->config->getChannel($salesChannelId);
        $endpoint   = $navigationRequest ? 'navigation' : 'search';

        return "rest/{$apiVersion}/{$endpoint}/{$channel}?{$paramString}";
    }
}
