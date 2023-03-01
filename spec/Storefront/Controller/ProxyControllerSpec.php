<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Storefront\Controller;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Stream;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientInterface;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Events\BeforeProxyErrorResponseEvent;
use Omikron\FactFinder\Shopware6\Events\EnrichProxyDataEvent;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProxyControllerSpec extends ObjectBehavior
{
    private Collaborator $client;
    private Collaborator $clientBuilder;

    public function let(
        Communication $config,
        ClientInterface $client,
        ClientBuilder $clientBuilder
    ) {
        $serverUrl = 'https://example.fact-finder.de/fact-finder';
        $config->getServerUrl()->willReturn($serverUrl);
        $config->getVersion()->willReturn('ng');
        $config->getCredentials()->willReturn([
            'username',
            'pass',
        ]);
        $this->beConstructedWith($config);
        $clientBuilder->build()->willReturn($client);
        $clientBuilder->withServerUrl(Argument::any())->willReturn($clientBuilder);
        $clientBuilder->withCredentials(Argument::any())->willReturn($clientBuilder);
        $clientBuilder->withVersion(Argument::any())->willReturn($clientBuilder);
        $this->client = $client;
        $this->clientBuilder = $clientBuilder;
    }

    public function it_should_return_success_response(
        Request $request,
        ResponseInterface $response,
        EventDispatcherInterface $eventDispatcher,
        EnrichProxyDataEvent $event,
        Stream $stream
    ) {
        // Expect & Given
        $request->getMethod()->willReturn(Request::METHOD_GET);
        $uri = 'rest/v5/search/example_channel?query=bag&sid=123&format=json';
        $_SERVER['REQUEST_URI'] = sprintf('/fact-finder/proxy/%s', $uri);
        $this->client->request(Request::METHOD_GET, $uri)->willReturn($response);
        $jsonResponse = file_get_contents(dirname(__DIR__, 2) . '/data/proxy/search-bag.json');
        $responseData = json_decode($jsonResponse, true);
        $stream->__toString()->willReturn($jsonResponse);
        $response->getBody()->willReturn($stream);
        $event->getData()->willReturn($responseData);
        $eventDispatcher->dispatch(Argument::type(EnrichProxyDataEvent::class))->willReturn($event);

        // When
        $response = $this->execute('rest/v5/search/example_channel', $request, $this->clientBuilder, $eventDispatcher);

        // Then
        $response->shouldBeAnInstanceOf(JsonResponse::class);
        Assert::assertEquals($responseData, json_decode($response->getWrappedObject()->getContent(), true));
    }

    public function it_should_return_error_response(
        Request $request,
        EventDispatcherInterface $eventDispatcher,
        BeforeProxyErrorResponseEvent $event,
        RequestInterface $requestInterface
    ) {
        // Expect & Given
        $request->getMethod()->willReturn(Request::METHOD_GET);
        $uri = 'rest/v5/search/example_channel?query=bag&sid=123&format=json';
        $_SERVER['REQUEST_URI'] = sprintf('/fact-finder/proxy/%s', $uri);
        $this->client->request(Request::METHOD_GET, $uri)->willThrow(new ConnectException('Unable to connect with server.', $requestInterface->getWrappedObject()));
        $eventDispatcher->dispatch(Argument::type(BeforeProxyErrorResponseEvent::class))->willReturn($event);

        // When
        $response = $this->execute('rest/v5/search/example_channel', $request, $this->clientBuilder, $eventDispatcher);

        // Then
        $response->shouldBeAnInstanceOf(JsonResponse::class);
        Assert::assertEquals(
            ['message' => 'Unable to connect with server.'],
            json_decode($response->getWrappedObject()->getContent(), true)
        );
        Assert::assertEquals(
            Response::HTTP_BAD_REQUEST,
            $response->getWrappedObject()->getStatusCode()
        );
    }
}
