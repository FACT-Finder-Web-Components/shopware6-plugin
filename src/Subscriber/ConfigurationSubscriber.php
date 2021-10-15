<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Config\Communication;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Storefront\Page\GenericPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigurationSubscriber implements EventSubscriberInterface
{
    private Communication $config;

    private array $fieldRoles;

    private array $communicationParameters;

    public function __construct(Communication $config, array $fieldRoles, array $communicationParameters)
    {
        $this->config                  = $config;
        $this->fieldRoles              = $fieldRoles;
        $this->communicationParameters = $communicationParameters;
    }

    public static function getSubscribedEvents()
    {
        return [GenericPageLoadedEvent::class => 'onPageLoaded'];
    }

    public function onPageLoaded(GenericPageLoadedEvent $event): void
    {
        $customer       = $event->getSalesChannelContext()->getCustomer();
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannel()->getId();

        $event->getPage()->addExtension('factfinder', new ArrayEntity([
            'field_roles'   => $this->fieldRoles,
            'communication' => [
                'url'                   => $this->config->getServerUrl(),
                'channel'               => $this->config->getChannel($salesChannelId),
                'version'               => 'ng',
                'api'                   => 'v4',
                'user-id'               => $customer ? $customer->getId() : null,
                'currency-code'         => $event->getSalesChannelContext()->getCurrency()->getIsoCode(),
                'currency-country-code' => $event->getRequest()->getLocale(),
                'search-immediate'      => strpos($event->getRequest()->get('_route'), 'factfinder') ? 'true' : 'false',
            ] + $this->communicationParameters,
        ]));
    }
}
