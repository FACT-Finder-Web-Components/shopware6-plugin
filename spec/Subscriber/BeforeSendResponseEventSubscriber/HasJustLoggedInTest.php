<?php

declare(strict_types=1);

namespace Subscriber\BeforeSendResponseEventSubscriber;

use Exception;
use Omikron\FactFinder\Shopware6\Subscriber\BeforeSendResponseEventSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Event\BeforeSendResponseEvent;
use Shopware\Storefront\Framework\Routing\StorefrontResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HasJustLoggedInTest extends TestCase
{
    private MockObject $subscriber;
    private MockObject $request;
    private MockObject $response;
    private MockObject $event;

    protected function setUp(): void
    {
        $this->subscriber = $this->getMockBuilder(BeforeSendResponseEventSubscriber::class)
            ->onlyMethods(['getCookie', 'validateRequest'])
            ->getMock();
        $this->request = $this->createMock(Request::class);
        $this->response = $this->createMock(StorefrontResponse::class);
        $this->event = $this->getMockBuilder(BeforeSendResponseEvent::class)
            ->setConstructorArgs([$this->request, $this->response])
            ->getMock();
    }

    public function testShouldNotPassRequestValidationWhenAjaxRequest()
    {
        // Expect & Given
        $this->request->method('isXmlHttpRequest')->willReturn(true);
        $this->response->method('getStatusCode')->willReturn(200);
        $this->subscriber->expects($this->once())->method('validateRequest')->willThrowException(new Exception('Not supported request'));

        // When & Then
        $this->subscriber->hasJustLoggedIn($this->event);
    }

    public function testShouldNotPassRequestValidationWhenUnsupportedResponseCode()
    {
        // Expect & Given
        $this->response->method('getStatusCode')->willReturn(301);
        $this->subscriber->expects($this->once())->method('validateRequest')->willThrowException(new Exception('Not supported request'));

        // When & Then
        $this->subscriber->hasJustLoggedIn($this->event);
    }

    public function testShouldNotPassRequestValidationWhenResponseIsNotInstanceOfStorefrontResponse()
    {
        // Expect & Given
        $response = new Response();
        $this->subscriber->expects($this->once())->method('validateRequest')->willThrowException(new Exception('Not supported request'));
        $event = $this->getMockBuilder(BeforeSendResponseEvent::class)
            ->setConstructorArgs([$this->request, $response])
            ->getMock();

        // When & Then
        $this->subscriber->hasJustLoggedIn($event);
    }
}
