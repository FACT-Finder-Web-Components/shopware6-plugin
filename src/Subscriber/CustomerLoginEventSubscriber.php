<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CustomerLoginEventSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            CustomerLoginEvent::class => 'hasJustLoggedIn',
        ];
    }

    public function hasJustLoggedIn(): void
    {
        if ($this->requestStack->getMainRequest() === null) {
            return;
        }

        $session = $this->requestStack->getMainRequest()->getSession();
        $session->set(BeforeSendResponseEventSubscriber::HAS_JUST_LOGGED_IN, true);
    }
}
