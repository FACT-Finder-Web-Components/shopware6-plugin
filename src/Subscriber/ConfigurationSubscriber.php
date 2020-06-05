<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigurationSubscriber implements EventSubscriberInterface
{
    /** @var SystemConfigService */
    private $systemConfig;

    /** @var array */
    private $fieldRoles;

    public function __construct(SystemConfigService $systemConfig, array $fieldRoles)
    {
        $this->systemConfig = $systemConfig;
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
                'url'                   => $this->config('serverUrl'),
                'channel'               => $this->config('channel'),
                'currency-code'         => $event->getSalesChannelContext()->getCurrency()->getIsoCode(),
                'currency-country-code' => $event->getRequest()->getLocale(),
                'search-immediate'      => strpos($event->getRequest()->get('_route'), 'factfinder') ? 'true' : 'false',
            ],
        ]);
    }

    private function config(string $param): string
    {
        return (string) $this->systemConfig->get('OmikronFactFinder.config.' . $param);
    }
}
