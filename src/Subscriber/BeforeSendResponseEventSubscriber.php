<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Shopware\Core\Framework\Event\BeforeSendResponseEvent;
use Shopware\Storefront\Framework\Routing\StorefrontResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class BeforeSendResponseEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            BeforeSendResponseEvent::class => 'hasJustLoggedIn',
        ];
    }

    public function hasJustLoggedIn(BeforeSendResponseEvent $event): void
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $response = $event->getResponse();

        if (
            !$response instanceof StorefrontResponse
            || $request->isXmlHttpRequest()
            || $response->getStatusCode() >= Response::HTTP_MULTIPLE_CHOICES
        ) {
            return;
        }

        if ((bool) $request->cookies->get('ff_has_just_logged_in', false) === true) {
            $response->headers->clearCookie('ff_has_just_logged_in');

            return;
        }

        if ($session->get('ff_has_just_logged_in', false) === true) {
            $cookie = Cookie::create('ff_has_just_logged_in')
                ->withValue('1')
                ->withExpires((new \DateTime())->modify('+1 hour')->getTimestamp())
                ->withHttpOnly(false);
            $response->headers->setCookie($cookie);
            $session->set('ff_has_just_logged_in', false);
        }
    }
}
