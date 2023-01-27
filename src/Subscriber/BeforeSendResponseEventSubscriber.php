<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use DateTime;
use Exception;
use Shopware\Core\Framework\Event\BeforeSendResponseEvent;
use Shopware\Storefront\Framework\Routing\StorefrontResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BeforeSendResponseEventSubscriber implements EventSubscriberInterface
{
    public const HAS_JUST_LOGGED_IN  = 'ff_has_just_logged_in';
    public const HAS_JUST_LOGGED_OUT = 'ff_has_just_logged_out';
    public const USER_ID             = 'ff_user_id';

    public static function getSubscribedEvents()
    {
        return [
            BeforeSendResponseEvent::class => [
                ['isCustomerLoggedOut'],
                ['isHasJustLoggedInCookieSet'],
                ['hasCustomerJustLoggedIn'],
                ['hasCustomerJustLoggedOut'],
            ],
        ];
    }

    public function isCustomerLoggedOut(BeforeSendResponseEvent $event): bool
    {
        $response = $event->getResponse();

        if (
            method_exists($response, 'getContext') === false
            || $response->getContext() === null
        ) {
            return false;
        }

        try {
            $this->validateRequest($event->getRequest(), $response);

            if ($response->getContext()->getCustomer() === null) {
                $response->headers->clearCookie(self::USER_ID);
                $response->headers->clearCookie(self::HAS_JUST_LOGGED_OUT);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function isHasJustLoggedInCookieSet(BeforeSendResponseEvent $event): bool
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        try {
            $this->validateRequest($request, $response);

            if ((bool) $request->cookies->get(self::HAS_JUST_LOGGED_IN, false) === true) {
                $response->headers->clearCookie(self::HAS_JUST_LOGGED_IN);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function hasCustomerJustLoggedIn(BeforeSendResponseEvent $event): bool
    {
        $request  = $event->getRequest();

        try {
            $session = $request->getSession();
        } catch (Exception $e) {
            return false;
        }

        $response = $event->getResponse();

        if (
            method_exists($response, 'getContext') === false
            || $response->getContext() === null
        ) {
            return false;
        }

        try {
            $this->validateRequest($request, $response);

            if ($session->get(self::HAS_JUST_LOGGED_IN, false) === true) {
                $response->headers->setCookie($this->getCookie(self::HAS_JUST_LOGGED_IN, '1'));
                $customer = $response->getContext()->getCustomer();
                if ($customer !== null) {
                    $response->headers->setCookie($this->getCookie(self::USER_ID, $customer->getId()));
                }
                $session->set(self::HAS_JUST_LOGGED_IN, false);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function hasCustomerJustLoggedOut(BeforeSendResponseEvent $event): bool
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        try {
            $session = $request->getSession();
        } catch (Exception $e) {
            return false;
        }

        try {
            $this->validateRequest($request, $response);

            if ($session->get(self::HAS_JUST_LOGGED_OUT, false) === true) {
                $response->headers->setCookie($this->getCookie(self::HAS_JUST_LOGGED_OUT, '1'));
                $response->headers->clearCookie(self::USER_ID);
                $session->set(self::HAS_JUST_LOGGED_OUT, false);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function getCookie(string $name, string $value): Cookie
    {
        return Cookie::create($name)
            ->withValue($value)
            ->withExpires((new DateTime())->modify('+1 day')->getTimestamp())
            ->withHttpOnly(false);
    }

    /**
     * @throws Exception
     */
    protected function validateRequest(Request $request, Response $response): void
    {
        if (
            !$response instanceof StorefrontResponse
            || $request->isXmlHttpRequest()
            || $response->getStatusCode() >= Response::HTTP_MULTIPLE_CHOICES
        ) {
            throw new Exception('Not supported request');
        }
    }
}
