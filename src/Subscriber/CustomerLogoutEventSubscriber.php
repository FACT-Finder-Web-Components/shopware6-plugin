<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Shopware\Core\Checkout\Customer\Event\CustomerLogoutEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CustomerLogoutEventSubscriber implements EventSubscriberInterface
{
    private ?RequestStack $requestStack;

    public function __construct(?RequestStack $requestStack = null)
    {
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            CustomerLogoutEvent::class => 'hasJustLoggedOut',
        ];
    }

    public function hasJustLoggedOut(): void
    {
        if (
            !isset($this->requestStack)
            || $this->requestStack->getMainRequest() === null
        ) {
            return;
        }

        $session = $this->requestStack->getMainRequest()->getSession();
        $session->set(BeforeSendResponseEventSubscriber::HAS_JUST_LOGGED_OUT, true);
    }
}
