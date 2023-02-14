<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Storefront\Controller;

use Exception;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Credentials;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Psr\Http\Client\ClientExceptionInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class ProxyController extends StorefrontController
{
    private Communication $config;

    public function __construct(Communication $config)
    {
        $this->config = $config;
    }

    /**
     * @Route(path="/fact-finder/proxy/{endpoint}", name="frontend.factfinder.proxy.execute", methods={"GET", "POST"}, requirements={"endpoint"=".*"})
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function execute(
        string        $endpoint,
        Request       $request,
        ClientBuilder $clientBuilder
    ): Response {
        $client = $clientBuilder
            ->withServerUrl($this->config->getServerUrl())
            ->withCredentials(new Credentials(...$this->config->getCredentials()))
            ->withVersion($this->config->getVersion())
            ->build();
        $query = (string)parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
        $method = $request->getMethod();

        try {
            switch ($method) {
                case 'GET':
                    $response = $client->request('GET', sprintf('%s?%s', $endpoint, $query));
                    break;
                case 'POST':
                    $response = $client->request('POST', $endpoint, [
                        'body'    => $request->getContent(),
                        'headers' => ['Content-Type' => 'application/json'],
                    ]);
                    break;
                default:
                    throw new Exception(sprintf('HTTP Method %s is not supported', $method));
            }

            return new JsonResponse(json_decode((string) $response->getBody(), true));
        } catch (ClientExceptionInterface $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
