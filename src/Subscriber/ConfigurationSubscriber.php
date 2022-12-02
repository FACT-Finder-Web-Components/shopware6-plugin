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
            'version'               => $this->config->getVersion(),
            'api'                   => 'v4',
            'currency-code'         => $event->getSalesChannelContext()->getCurrency()->getIsoCode(),
            'currency-country-code' => $event->getRequest()->getLocale(),
        ];

        if (!empty($this->addParams)) {
            $communication['add-params'] = implode(',', $this->addParams);
        }

        try {
            $page = $this->getPage($event);
        } catch (\Exception $e) {
            return;
        }

        if ($page->hasExtension('factfinder') === false) {
            $page->addExtension('factfinder', new ArrayEntity([
                'field_roles'     => $this->config->getFieldRoles($salesChannelId) ?: $this->fieldRoles,
                'communication'   => $communication + $this->communicationParameters,
                'searchImmediate' => $this->isSearchImmediate($event) ? 'true' : 'false',
                'userId'          => $customer ? $customer->getId() : null,
                'ssr'             => $this->config->isSsrActive(),
            ]));
        }
    }

    private function getPage(ShopwareSalesChannelEvent $event)
    {
        if (method_exists($event, 'getPage')) {
            return $event->getPage();
        }

        if (isset($event->getParameters()['page'])) {
            return $event->getParameters()['page'];
        }

        throw new \Exception(sprintf('Unable to get page from event %s.', get_class($event)));
    }

    private function isSearchImmediate(ShopwareSalesChannelEvent $event): bool
    {
        $request = $event->getRequest();

        if (
            $this->config->isSsrActive()
            || $request->isXmlHttpRequest()
        ) {
            return false;
        }

        $route = $event->getRequest()->get('_route', '');

        return $this->isSearchPage($route);
    }

    private function isSearchPage(string $route): bool
    {
        return strpos($route ?? '', 'factfinder') !== false;
    }
}
