<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Config\Communication;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Storefront\Page\GenericPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigurationSubscriber implements EventSubscriberInterface
{
    /** @var Communication */
    private $config;

    /** @var array */
    private $fieldRoles;

    public function __construct(Communication $config, array $fieldRoles)
    {
        $this->config     = $config;
        $this->fieldRoles = $fieldRoles;
    }

    public static function getSubscribedEvents()
    {
        return [GenericPageLoadedEvent::class => 'onPageLoaded'];
    }

    public function onPageLoaded(GenericPageLoadedEvent $event): void
    {
        $event->getPage()->addExtension('factfinder', new ArrayEntity([
            'field_roles'   => $this->fieldRoles,
            'communication' => [
                'url'                   => $this->config->getServerUrl(),
                'channel'               => $this->config->getChannel(),
                'version'               => 'ng',
                'api'                   => 'v3',
                'currency-code'         => $event->getSalesChannelContext()->getCurrency()->getIsoCode(),
                'currency-country-code' => $event->getRequest()->getLocale(),
                'search-immediate'      => strpos($event->getRequest()->get('_route'), 'factfinder') ? 'true' : 'false',
            ],
        ]));
    }
}
