<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Config\Communication;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigurationSubscriber implements EventSubscriberInterface
{
    /** @var Communication */
    private $config;

    /** @var array */
    private $fieldRoles;

    public function __construct(Communication $config, array $fieldRoles)
    {
        $this->config       = $config;
        $this->fieldRoles   = $fieldRoles;
    }

    public static function getSubscribedEvents()
    {
        return [
            StorefrontRenderEvent::class => 'onRenderStorefront',
        ];
    }

    public function onRenderStorefront(StorefrontRenderEvent $event): void
    {
        $event->setParameter('factfinder', [
            'field_roles'   => $this->fieldRoles,
            'communication' => [
                'url'                   => $this->config->getServerUrl(),
                'channel'               => $this->config->getChannel(),
                'currency-code'         => $event->getSalesChannelContext()->getCurrency()->getIsoCode(),
                'currency-country-code' => $event->getRequest()->getLocale(),
                'search-immediate'      => strpos($event->getRequest()->get('_route'), 'factfinder') ? 'true' : 'false',
            ],
        ]);
    }
}
