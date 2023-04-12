<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Exception;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Config\ExtensionConfig;
use Shopware\Core\Framework\Event\ShopwareSalesChannelEvent;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Shopware\Storefront\Page\GenericPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ConfigurationSubscriber implements EventSubscriberInterface
{
    private Communication $config;
    private ExtensionConfig $extensionConfig;
    private array $fieldRoles;
    private array $communicationParameters;
    private array $addParams;

    public function __construct(
        Communication $config,
        ExtensionConfig $extensionConfig,
        RouterInterface $router,
        array $fieldRoles,
        array $communicationParameters,
        array $configurationAddParams = []
    ) {
        $this->config                  = $config;
        $this->extensionConfig         = $extensionConfig;
        $this->router                  = $router;
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
            'url'                   => $this->getServerUrl(),
            'channel'               => $this->config->getChannel($salesChannelId),
            'version'               => $this->config->getVersion(),
            'api'                   => $this->config->getApiVersion(),
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

        $communicationConfig = $communication + $this->communicationParameters;

        if ($page->hasExtension('factfinder') === false) {
            $page->addExtension('factfinder', new ArrayEntity([
                'field_roles'             => $this->config->getFieldRoles($salesChannelId) ?: $this->fieldRoles,
                'communication'           => $communicationConfig,
                'trackingSettings'        => $this->extensionConfig->getTrackingSettings(),
                'redirectMapping'         => (string) $this->extensionConfig->getRedirectMapping(),
                'searchImmediate'         => $this->isSearchImmediate($event) ? 'true' : 'false',
                'userId'                  => $customer ? $customer->getId() : null,
                'ssr'                     => $this->config->isSsrActive(),
                'communicationAttributes' => $this->getCommunicationAttributes($communicationConfig),
            ]));
        }
    }

    private function getPage(ShopwareSalesChannelEvent $event): Struct
    {
        if (method_exists($event, 'getPage')) {
            return $event->getPage();
        }

        $parameters = method_exists($event, 'getParameters') && is_array($event->getParameters()) ? $event->getParameters() : [];

        if (
            isset($parameters['page'])
            && $parameters['page'] instanceof Struct
        ) {
            return $parameters['page'];
        }

        throw new Exception(sprintf('Unable to get page from event %s.', get_class($event)));
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

    private function getServerUrl(): string
    {
        if ($this->config->isProxyEnabled()) {
            return $this->router->generate(
                'frontend.factfinder.proxy.execute',
                ['endpoint' => ''],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        return $this->config->getServerUrl();
    }

    private function getCommunicationAttributes(array $communicationConfig): array
    {
        return array_map(
            fn (string $key, string $value) => sprintf('%s="%s" ', $key, htmlspecialchars($value)),
            array_keys($communicationConfig),
            array_values($communicationConfig)
        );
    }
}
