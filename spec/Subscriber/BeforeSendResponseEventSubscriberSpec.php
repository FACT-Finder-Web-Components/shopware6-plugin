<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Subscriber;

use DateTime;
use Omikron\FactFinder\Shopware6\Subscriber\BeforeSendResponseEventSubscriber;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use Prophecy\Argument;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\Event\BeforeSendResponseEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Framework\Routing\StorefrontResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;
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

    /** @var SessionInterface|Collaborator */
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
        $this->headers->setCookie(Cookie::create('ff_has_just_logged_out')
            ->withValue('0')
            ->withExpires((new DateTime())->modify('+1 day')->getTimestamp())
            ->withHttpOnly(false))->shouldBeCalled();
        $this->headers->setCookie(Cookie::create('ff_user_id')
            ->withValue('0')
            ->withExpires((new DateTime())->modify('+1 day')->getTimestamp())
            ->withHttpOnly(false))->shouldBeCalled();

        // When & Then
        $this->isCustomerLoggedOut($this->event);
    }

    public function it_should_clear_has_just_logged_in_cookie_when_is_already_set(ParameterBag $cookies)
    {
        // Expect & Given
        $this->request->cookies = $cookies;
        $cookies->get('ff_has_just_logged_in', Argument::any())->willReturn(true);
        $this->context->getCustomer()->willReturn(null);
        $this->headers->setCookie(Cookie::create('ff_has_just_logged_in')
            ->withValue('0')
            ->withExpires((new DateTime())->modify('+1 day')->getTimestamp())
            ->withHttpOnly(false))->shouldBeCalled();

        // When & Then
        $this->isHasJustLoggedInCookieSet($this->event);
    }

    public function it_should_set_has_just_logged_in_cookie_and_user_id_cookie(CustomerEntity $customer)
    {
        // Expect & Given
        $customer->getId()->willReturn('test_id_1');
        $this->context->getCustomer()->willReturn($customer);
        $this->session->get('ff_has_just_logged_in', Argument::any())->willReturn(true);
        $this->headers->setCookie(Cookie::create('ff_has_just_logged_in')
            ->withValue('1')
            ->withExpires((new DateTime())->modify('+1 day')->getTimestamp())
            ->withHttpOnly(false))->shouldBeCalled();
        $this->headers->setCookie(Cookie::create('ff_user_id')
            ->withValue('test_id_1')
            ->withExpires((new DateTime())->modify('+1 day')->getTimestamp())
            ->withHttpOnly(false))->shouldBeCalled();
        $this->session->set('ff_has_just_logged_in', false)->shouldBeCalled();

        // When & Then
        $this->hasCustomerJustLoggedIn($this->event);
    }

    public function it_should_set_has_just_logged_out_cookie()
    {
        // Expect & Given
        $this->session->get('ff_has_just_logged_out', Argument::any())->willReturn(true);
        $this->headers->setCookie(Cookie::create('ff_has_just_logged_out')
            ->withValue('1')
            ->withExpires((new DateTime())->modify('+1 day')->getTimestamp())
            ->withHttpOnly(false))->shouldBeCalled();
        $this->headers->setCookie(Cookie::create('ff_user_id')
            ->withValue('0')
            ->withExpires((new DateTime())->modify('+1 day')->getTimestamp())
            ->withHttpOnly(false))->shouldBeCalled();
        $this->session->set('ff_has_just_logged_out', false)->shouldBeCalled();

        // When & Then
        $this->hasCustomerJustLoggedOut($this->event);
    }
}
