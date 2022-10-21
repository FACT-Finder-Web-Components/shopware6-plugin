<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Subscriber;

use PhpSpec\ObjectBehavior;
use Shopware\Core\Framework\Event\BeforeSendResponseEvent;
use Shopware\Storefront\Framework\Routing\StorefrontResponse;
use Symfony\Component\HttpFoundation\Request;

class BeforeSendResponseEventSubscriberSpec extends ObjectBehavior
{
    public function testShouldNotPassRequestValidationWhenAjaxRequest(Request $request, StorefrontResponse $response, BeforeSendResponseEvent $event)
    {
        // Expect & Given
        $request->isXmlHttpRequest()->willReturn(false);
        $response->getStatusCode()->willReturn(200);
//        $this->subscriber->expects($this->once())->method('validateRequest')->willThrowException(new Exception('Not supported request'));
        $event->getRequest()->willReturn($request);
        $event->getResponse()->willReturn($response);

        // When & Then
        $this->hasCustomerJustLoggedIn($event)->shouldReturn(false);
    }
}
