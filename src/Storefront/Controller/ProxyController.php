<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Storefront\Controller;

use Exception;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Credentials;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Events\BeforeProxyErrorResponseEvent;
use Omikron\FactFinder\Shopware6\Events\EnrichProxyDataEvent;
use Psr\Http\Client\ClientExceptionInterface;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class ProxyController extends StorefrontController
{
    private Communication $config;

    public function __construct(Communication $config)
    {
        $this->config = $config;
    }

    /**
     * @Route(
     *     path="/fact-finder/proxy/{endpoint}",
     *     name="frontend.factfinder.proxy.execute",
     *     methods={"GET", "POST"},
     *     requirements={"endpoint"=".*"},
     *     defaults={"csrf_protected"=false}
     * )
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function execute(
        string $endpoint,
        Request $request,
        ClientBuilder $clientBuilder,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $client = $clientBuilder
            ->withServerUrl($this->config->getServerUrl())
            ->withCredentials(new Credentials(...$this->config->getCredentials()))
            ->withVersion($this->config->getVersion())
            ->build();
        $query  = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
        $method = $request->getMethod();

        try {
            switch ($method) {
                case Request::METHOD_GET:
                    $response = $client->request('GET', sprintf('%s?%s', $endpoint, $query));

                    break;
                case Request::METHOD_POST:
                    $response = $client->request('POST', $endpoint, [
                        'body'    => $request->getContent(),
                        'headers' => ['Content-Type' => 'application/json'],
                    ]);

                    break;
                default:
                    throw new Exception(sprintf('HTTP Method %s is not supported', $method));
            }

            $event = new EnrichProxyDataEvent(json_decode((string) $response->getBody(), true));
            $eventDispatcher->dispatch($event);

            return new JsonResponse($event->getData());
        } catch (ClientExceptionInterface $e) {
            $response = new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            $event    = new BeforeProxyErrorResponseEvent($response);
            $eventDispatcher->dispatch($event);

            return $event->getResponse();
        }
    }
}
