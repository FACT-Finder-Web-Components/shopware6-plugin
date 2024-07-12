<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BeforeSendResponseEventSubscriber implements EventSubscriberInterface
{
    public const HAS_JUST_LOGGED_IN  = 'ff_has_just_logged_in';
    public const HAS_JUST_LOGGED_OUT = 'ff_has_just_logged_out';
    public const USER_ID             = 'ff_user_id';

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [
                ['isHasJustLoggedInCookieSet'],
                ['hasCustomerJustLoggedIn'],
                ['hasCustomerJustLoggedOut'],
            ],
        ];
    }

    public function isHasJustLoggedInCookieSet(ResponseEvent $event): bool
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        try {
            $this->validateRequest($request, $response);

            if ((bool) $request->cookies->get(self::HAS_JUST_LOGGED_IN, false) === true) {
                $response->headers->clearCookie(self::HAS_JUST_LOGGED_IN);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function hasCustomerJustLoggedIn(ResponseEvent $event): bool
    {
        $request  = $event->getRequest();

        try {
            $session = $request->getSession();
        } catch (\Exception $e) {
            return false;
        }

        $response = $event->getResponse();

        try {
            $this->validateRequest($request, $response);

            if (empty($session->get(self::USER_ID, false))) {
                return false;
            }

            if ($session->get(self::HAS_JUST_LOGGED_IN, false) === true) {
                $response->headers->setCookie($this->getCookie(self::HAS_JUST_LOGGED_IN, '1'));
                $response->headers->setCookie($this->getCookie(self::USER_ID, $session->get(self::USER_ID, false)));
                $session->set(self::HAS_JUST_LOGGED_IN, false);
                $response->headers->clearCookie(self::HAS_JUST_LOGGED_OUT);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function hasCustomerJustLoggedOut(ResponseEvent $event): bool
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        try {
            $session = $request->getSession();
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getCookie(string $name, string $value): Cookie
    {
        return Cookie::create($name)
            ->withValue($value)
            ->withExpires((new \DateTime())->modify('+1 day')->getTimestamp())
            ->withHttpOnly(false);
    }

    /**
     * @throws \Exception
     */
    protected function validateRequest(Request $request, Response $response): void
    {
        if (
            $request->isXmlHttpRequest()
            || $response->getStatusCode() >= Response::HTTP_MULTIPLE_CHOICES
        ) {
            throw new \Exception('Not supported request');
        }
    }
}
