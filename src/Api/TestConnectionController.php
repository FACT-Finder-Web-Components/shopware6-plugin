<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Api;

use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Credentials;
use Omikron\FactFinder\Shopware6\Config\Communication as CommunicationConfig;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
class TestConnectionController extends AbstractController
{
    public function __construct(
        private readonly ClientBuilder $clientBuilder,
        private readonly CommunicationConfig $config
    ) {
    }

    /**
     * @Route("/api/_action/test-connection/api", name="api.action.fact_finder.test_api_connection", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     */
    public function testApiConnection(): JsonResponse
    {
        $client = $this->clientBuilder
            ->withCredentials(new Credentials(...$this->config->getCredentials()))
            ->withServerUrl($this->config->getServerUrl())
            ->withVersion($this->config->getVersion())
            ->build();

        try {
            $endpoint = $this->createTestEndpoint();
            $client->request('GET', $endpoint);

            return new JsonResponse(['message' => 'Connection successfully established'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Connection could not be established'], 400);
        }
    }

    private function createTestEndpoint(): string
    {
        $apiVersion = $this->config->getApiVersion();
        $channel    = $this->config->getChannel();

        return "rest/{$apiVersion}/records/{$channel}/compare";
    }
}
