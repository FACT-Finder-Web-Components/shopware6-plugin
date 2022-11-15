<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Config\Communication;
use Shopware\Core\Framework\Event\ShopwareSalesChannelEvent;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Storefront\Event\StorefrontRenderEvent;
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
        return [
            GenericPageLoadedEvent::class => 'onPageLoaded',
            StorefrontRenderEvent::class  => 'onPageLoaded',
        ];
    }

    public function onPageLoaded(ShopwareSalesChannelEvent $event): void
    {
        $customer       = $event->getSalesChannelContext()->getCustomer();
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannel()->getId();
        $communication  = [
            'url'                   => $this->config->getServerUrl(),
            'channel'               => $this->config->getChannel($salesChannelId),
            'version'               => 'ng',
            'api'                   => 'v4',
            'currency-code'         => $event->getSalesChannelContext()->getCurrency()->getIsoCode(),
            'currency-country-code' => $event->getRequest()->getLocale(),
        ];

        if (!empty($this->addParams)) {
            $communication['add-params'] = implode(',', $this->addParams);
        }

        $page = method_exists($event, 'getPage') ? $event->getPage() : $event->getParameters()['page'];
        $page->addExtension('factfinder', new ArrayEntity([
            'field_roles'     => $this->config->getFieldRoles($salesChannelId) ?: $this->fieldRoles,
            'communication'   => $communication + $this->communicationParameters,
            'searchImmediate' => strpos($event->getRequest()->get('_route') ?? '', 'factfinder') ? 'true' : 'false',
            'userId'          => $customer ? $customer->getId() : null,
        ]));
    }
}
