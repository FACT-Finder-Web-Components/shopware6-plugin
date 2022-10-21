<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Subscriber;

use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use Shopware\Core\Framework\Event\BeforeSendResponseEvent;
use Shopware\Storefront\Framework\Routing\StorefrontResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BeforeSendResponseEventSubscriberSpec extends ObjectBehavior
{
    /** @var BeforeSendResponseEvent|Collaborator */
    private Collaborator $event;

    /** @var Request|Collaborator */
    private Collaborator $request;

    /** @var StorefrontResponse|Collaborator */
    private Collaborator $response;

    /** @var StorefrontResponse|Collaborator */
    private Collaborator $session;

    function let(
        Request $request,
        StorefrontResponse $response,
        BeforeSendResponseEvent $event,
        SessionInterface $session
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
        $this->request->getSession()->willReturn($this->session);
        $response->getStatusCode()->willReturn(200);
        $event->getRequest()->willReturn($request);
        $event->getResponse()->willReturn($response);
        $this->event = $event;
    }

    public function it_should_not_pass_request_validation_when_ajax_request()
    {
        // Expect & Given
        $this->request->isXmlHttpRequest()->willReturn(true);
        $this->response->getStatusCode()->willReturn(200);

        // When & Then
        $this->hasCustomerJustLoggedIn($this->event)->shouldReturn(false);
    }

    public function it_should_not_pass_request_validation_when_unsupported_response_code()
    {
        // Expect & Given
        $this->request->isXmlHttpRequest()->willReturn(false);
        $this->response->getStatusCode()->willReturn(300);

        // When & Then
        $this->hasCustomerJustLoggedIn($this->event)->shouldReturn(false);
    }

    public function it_should_not_pass_request_validation_when_response_is_not_instance_of_storefront_response(
        BeforeSendResponseEvent $event,
        Response $response
    ) {
        // Expect & Given
        $this->request->isXmlHttpRequest()->willReturn(false);
        $this->response->getStatusCode()->willReturn(200);
        $event->getRequest()->willReturn($this->request);
        $event->getResponse()->willReturn($response);

        // When & Then
        $this->hasCustomerJustLoggedIn($this->event)->shouldReturn(false);
    }
}
