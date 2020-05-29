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

    public function __construct(SystemConfigService $systemConfig)
    {
        $this->systemConfig = $systemConfig;
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
            'field_roles' => [
                'brand'         => 'Manufacturer',
                'deeplink'      => 'Deeplink',
                'description'   => 'Description',
                'ean'           => 'EAN',
                'imageUrl'      => 'ImageURL',
                'masterId'      => 'MasterArticleNumber',
                'price'         => 'Price',
                'productName'   => 'Title',
                'productNumber' => 'ArticleNumber',
            ],

            'communication' => [
                'currency-code'         => $event->getSalesChannelContext()->getCurrency()->getIsoCode(),
                'currency-country-code' => $event->getRequest()->getLocale(),
                'url'                   => $this->config('serverUrl'),
                'channel'               => $this->config('channel'),
            ],
        ]);
    }

    private function config(string $param): string
    {
        return (string) $this->systemConfig->get('OmikronFactFinder.config.' . $param);
    }
}
