<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Storefront\Controller;

use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientInterface;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Events\EnrichProxyDataEvent;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProxyControllerSpec extends ObjectBehavior
{
    private Collaborator $config;

    public function let(Communication $config) {
        $config->getServerUrl()->willReturn('https://example.fact-finder.de/fact-finder');
        $config->getVersion()->willReturn('ng');
        $config->getCredentials()->willReturn([
            'username',
            'pass',
        ]);
        $this->beConstructedWith($config);
    }

    public function it_should_return_original_get_response(
        Request       $request,
        ClientBuilder $clientBuilder,
        ClientInterface $client,
        ResponseInterface $response,
        EventDispatcherInterface $eventDispatcher,
        EnrichProxyDataEvent $event
    ) {
        $request->getMethod()->willReturn(Request::METHOD_GET);
        $clientBuilder->build()->willReturn($client);
        $uri = 'rest/v5/search/example_channel?query=bag&sid=123&format=json';
        $client->request(Request::METHOD_GET, $uri)->willReturn($response);
        $event->getData()->willReturn([
            'hits' => [],
        ]);

        $this->shouldBeAnInstanceOf(JsonResponse::class);
    }
}
