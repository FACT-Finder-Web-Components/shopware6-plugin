<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr;

use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientException;
use Omikron\FactFinder\Communication\Credentials;
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
        PriceFormatter $priceFormatter
    ) {
        $this->clientBuilder  = $clientBuilder;
        $this->config         = $config;
        $this->priceFormatter = $priceFormatter;
    }

    public function search(string $query, bool $navigationRequest): array
    {
        $client = $this->clientBuilder
            ->withServerUrl($this->config->getServerUrl())
            ->withCredentials(new Credentials(...$this->config->getCredentials()))
            ->withVersion($this->config->getVersion())
            ->build();

        $endpoint = $this->createEndpoint($query, $navigationRequest);
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

    private function createEndpoint(string $query, bool $navigationRequest)
    {
        $channel  = $this->config->getChannel();
        $endpoint = $navigationRequest ? 'navigation' : 'search';

        return "rest/v4/{$endpoint}/{$channel}?query={$query}";
    }
}
