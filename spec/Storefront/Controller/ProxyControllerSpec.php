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
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
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
        ClientBuilder $clientBuilder,
        Request $request,
        HeaderBag $headerBag
    ): void {
        $serverUrl = 'https://example.fact-finder.de/fact-finder';

        // Konfiguracja zachowania mocka Communication
        $config->getServerUrl()->willReturn($serverUrl);
        $config->getVersion()->willReturn('ng');
        $config->getApiKey()->willReturn('api-key-123');
        $config->isProxyEnabled()->willReturn(true);

        $this->beConstructedWith($config);

        $clientBuilder->build()->willReturn($client);
        $clientBuilder->withServerUrl(Argument::any())->willReturn($clientBuilder);
        $clientBuilder->withApiKey(Argument::any())->willReturn($clientBuilder);
        $clientBuilder->withVersion(Argument::any())->willReturn($clientBuilder);

        $request->headers = $headerBag;
        $headerBag->get('x-ff-api-key')->willReturn('api-key-123');

        $this->client        = $client;
        $this->clientBuilder = $clientBuilder;
    }

    public function it_should_return_unauthorized_if_api_key_header_is_missing(
        Request $request,
        HeaderBag $headerBag,
        ClientBuilder $clientBuilder,
        EventDispatcherInterface $eventDispatcher
    ): void {
        // Given
        $request->headers = $headerBag;
        $headerBag->get('x-ff-api-key')->willReturn(null);

        // When
        $response = $this->execute('some/endpoint', $request, $clientBuilder, $eventDispatcher);

        // Then
        $response->shouldBeAnInstanceOf(JsonResponse::class);
        Assert::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getWrappedObject()->getStatusCode());
        Assert::assertStringContainsString('UNAUTHORIZED', $response->getWrappedObject()->getContent());
    }

    public function it_should_return_success_response(
        Request $request,
        ResponseInterface $response,
        EventDispatcherInterface $eventDispatcher,
        EnrichProxyDataEvent $event,
        Stream $stream
    ): void {
        // Expect & Given
        $request->getMethod()->willReturn(Request::METHOD_GET);
        $uri                    = 'rest/v5/search/example_channel?query=bag&sid=123&format=json';
        $_SERVER['REQUEST_URI'] = sprintf('/fact-finder/proxy/%s', $uri);
        $this->clientBuilder->withApiKey('api-key-123')->shouldBeCalled()->willReturn($this->clientBuilder);

        $this->client->request(Request::METHOD_GET, $uri)->willReturn($response);
        $jsonResponse = '{"results": []}'; // Uproszczone dla przykładu, możesz zostawić file_get_contents
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
    ): void {
        // Expect & Given
        $request->getMethod()->willReturn(Request::METHOD_GET);
        $uri                    = 'rest/v5/search/example_channel?query=bag&sid=123&format=json';
        $_SERVER['REQUEST_URI'] = sprintf('/fact-finder/proxy/%s', $uri);

        $this->client->request(Request::METHOD_GET, $uri)->willThrow(new ConnectException('Unable to connect with server.', $requestInterface->getWrappedObject()));
        $eventDispatcher->dispatch(Argument::type(BeforeProxyErrorResponseEvent::class))->willReturn($event);
        $event->getResponse()->willReturn(new JsonResponse(['message' => 'Unable to connect with server.'], Response::HTTP_BAD_REQUEST));

        // When
        $response = $this->execute('rest/v5/search/example_channel', $request, $this->clientBuilder, $eventDispatcher);

        // Then
        $response->shouldBeAnInstanceOf(JsonResponse::class);
        Assert::assertEquals(Response::HTTP_BAD_REQUEST, $response->getWrappedObject()->getStatusCode());
    }

    public function it_should_throw_exception_if_proxy_is_disabled(
        Communication $config,
        Request $request,
        ClientBuilder $clientBuilder,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $config->isProxyEnabled()->willReturn(false);
        $this->shouldThrow(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class)
            ->during('execute', ['some-endpoint', $request, $clientBuilder, $eventDispatcher]);
    }
}
