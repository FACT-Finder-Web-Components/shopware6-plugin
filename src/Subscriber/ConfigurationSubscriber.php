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

    private array $addParams;

    public function __construct(
        Communication $config,
        array $fieldRoles,
        array $communicationParameters,
        array $configurationAddParams = []
    ) {
        $this->config                  = $config;
        $this->fieldRoles              = $fieldRoles;
        $this->communicationParameters = $communicationParameters;
        $this->addParams               = $configurationAddParams;
    }

    public static function getSubscribedEvents()
    {
        return [GenericPageLoadedEvent::class => 'onPageLoaded'];
    }

    public function onPageLoaded(GenericPageLoadedEvent $event): void
    {
        $customer       = $event->getSalesChannelContext()->getCustomer();
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannel()->getId();
        $communication  = [
            'url'                   => $this->config->getServerUrl(),
            'channel'               => $this->config->getChannel($salesChannelId),
            'version'               => 'ng',
            'api'                   => 'v4',
            'user-id'               => $customer ? $customer->getId() : null,
            'currency-code'         => $event->getSalesChannelContext()->getCurrency()->getIsoCode(),
            'currency-country-code' => $event->getRequest()->getLocale(),
            'search-immediate'      => strpos($event->getRequest()->get('_route'), 'factfinder') ? 'true' : 'false',
        ];

        if (!empty($this->addParams)) {
            $communication['add-params'] = implode(',', $this->addParams);
        }

        $event->getPage()->addExtension('factfinder', new ArrayEntity([
            'field_roles'   => $this->fieldRoles,
            'communication' => $communication + $this->communicationParameters,
        ]));
    }
}
