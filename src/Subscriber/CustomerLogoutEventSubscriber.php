<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Shopware\Core\Checkout\Customer\Event\CustomerLogoutEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class CustomerLogoutEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private ?RequestStack $requestStack = null)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            CustomerLogoutEvent::class => 'hasJustLoggedOut',
        ];
    }

    public function hasJustLoggedOut(): void
    {
        if (!isset($this->requestStack)
            || $this->requestStack->getMainRequest() === null
        ) {
            return;
        }

        $session = $this->requestStack->getMainRequest()->getSession();
        $session->set(BeforeSendResponseEventSubscriber::HAS_JUST_LOGGED_OUT, true);
        $session->remove(BeforeSendResponseEventSubscriber::USER_ID);
    }
}
