<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CustomerLoginEventSubscriber implements EventSubscriberInterface
{
    private ?RequestStack $requestStack;

    public function __construct(?RequestStack $requestStack = null)
    {
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            CustomerLoginEvent::class => 'hasJustLoggedIn',
        ];
    }

    public function hasJustLoggedIn(CustomerLoginEvent $event): void
    {
        if (
            !isset($this->requestStack)
            || $this->requestStack->getMainRequest() === null
            || empty($event->getCustomer()->getId())
        ) {
            return;
        }

        $session = $this->requestStack->getMainRequest()->getSession();
        $session->set(BeforeSendResponseEventSubscriber::HAS_JUST_LOGGED_IN, true);
        $session->set(BeforeSendResponseEventSubscriber::USER_ID, $event->getCustomer()->getId());
    }
}
