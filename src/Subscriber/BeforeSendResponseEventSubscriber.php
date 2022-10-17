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
    private const USER_ID            = 'ff_user_id';

    public static function getSubscribedEvents()
    {
        return [
            BeforeSendResponseEvent::class => [
                ['hasJustLoggedIn'],
                ['hasJustLoggedOut'],
            ],
        ];
    }

    public function hasJustLoggedIn(BeforeSendResponseEvent $event): void
    {
        $request  = $event->getRequest();
        $session  = $request->getSession();
        $response = $event->getResponse();

        try {
            $this->validateRequest($request, $response);
        } catch (Exception $e) {
            return;
        }

        if ($response->getContext()->getCustomer() === null) {
            $response->headers->clearCookie(self::USER_ID);
            $response->headers->clearCookie(self::HAS_JUST_LOGGED_OUT);
        }

        if ((bool) $request->cookies->get(self::HAS_JUST_LOGGED_IN, false) === true) {
            $response->headers->clearCookie(self::HAS_JUST_LOGGED_IN);

            return;
        }

        if ($session->get(self::HAS_JUST_LOGGED_IN, false) === true) {
            $response->headers->setCookie($this->getCookie(self::HAS_JUST_LOGGED_IN, '1'));
            $response->headers->setCookie($this->getCookie(self::USER_ID, $response->getContext()->getCustomer()->getId()));
            $session->set(self::HAS_JUST_LOGGED_IN, false);
        }
    }

    public function hasJustLoggedOut(BeforeSendResponseEvent $event): void
    {
        $request  = $event->getRequest();
        $session  = $request->getSession();
        $response = $event->getResponse();

        try {
            $this->validateRequest($request, $response);
        } catch (Exception $e) {
            return;
        }

        if ($session->get(self::HAS_JUST_LOGGED_OUT, false) === true) {
            $response->headers->setCookie($this->getCookie(self::HAS_JUST_LOGGED_OUT, '1'));
            $response->headers->clearCookie(self::USER_ID);
            $session->set(self::HAS_JUST_LOGGED_OUT, false);
        }
    }

    protected function getCookie(string $name, string $value): Cookie
    {
        return Cookie::create($name)
            ->withValue($value)
            ->withExpires((new DateTime())->modify('+1 hour')->getTimestamp())
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
