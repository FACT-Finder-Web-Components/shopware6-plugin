<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Subscriber\BeforeSendResponseEventSubscriber;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use Shopware\Core\Framework\Event\BeforeSendResponseEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Framework\Routing\StorefrontResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

    /** @var ResponseHeaderBag|Collaborator */
    private Collaborator $headers;

    /** @var SalesChannelContext|Collaborator */
    private Collaborator $context;

    private array $subscriberMethods;

    function let(
        Request $request,
        StorefrontResponse $response,
        BeforeSendResponseEvent $event,
        SessionInterface $session,
        ResponseHeaderBag $headers,
        SalesChannelContext $context
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
        $this->event = $event;
        $this->headers = $headers;
        $this->context = $context;

        $this->request->getSession()->willReturn($this->session);
        $this->request->isXmlHttpRequest()->willReturn(false);

        $this->response->getStatusCode()->willReturn(200);
        $this->response->getContext()->willReturn($this->context);
        $this->response->headers = $this->headers;

        $event->getRequest()->willReturn($request);
        $event->getResponse()->willReturn($response);

        $this->subscriberMethods = array_map(
            fn (array $subscriberMethod) => $subscriberMethod[0],
            (new BeforeSendResponseEventSubscriber())->getSubscribedEvents()[BeforeSendResponseEvent::class]
        );
    }

    public function it_should_not_pass_request_validation_when_ajax_request()
    {
        foreach ($this->subscriberMethods as $subscriberMethod) {
            // Expect & Given
            $this->request->isXmlHttpRequest()->willReturn(true);
            $this->response->getStatusCode()->willReturn(200);

            // When & Then
            $this->$subscriberMethod($this->event)->shouldReturn(false);
        }
    }

    public function it_should_not_pass_request_validation_when_unsupported_response_code()
    {
        foreach ($this->subscriberMethods as $subscriberMethod) {
            // Expect & Given
            $this->request->isXmlHttpRequest()->willReturn(false);
            $this->response->getStatusCode()->willReturn(300);

            // When & Then
            $this->$subscriberMethod($this->event)->shouldReturn(false);
        }
    }

    public function it_should_not_pass_request_validation_when_response_is_not_instance_of_storefront_response(
        BeforeSendResponseEvent $event,
        Response $symfonyResponse
    ) {
        foreach ($this->subscriberMethods as $subscriberMethod) {
            // Expect & Given
            $event->getRequest()->willReturn($this->request);
            $event->getResponse()->willReturn($symfonyResponse);

            // When & Then
            $this->$subscriberMethod($this->event)->shouldReturn(false);
        }
    }

    public function it_should_clear_user_id_cookie_and_has_just_logged_out_cookie_when_customer_logged_out()
    {
        // Expect & Given
        $this->context->getCustomer()->willReturn(null);
        $this->headers->clearCookie('ff_user_id')->shouldBeCalled();
        $this->headers->clearCookie('ff_has_just_logged_out')->shouldBeCalled();

        // When & Then
        $this->isCustomerLoggedOut($this->event);
    }
}
