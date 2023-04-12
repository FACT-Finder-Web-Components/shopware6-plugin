<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Subscriber;

use Exception;
use Omikron\FactFinder\Communication\Version;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Config\ExtensionConfig;
use Omikron\FactFinder\Shopware6\Domain\RedirectMapping;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Argument\Token\LogicalAndToken;
use Prophecy\Argument\Token\TypeToken;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\System\Currency\CurrencyEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Shopware\Storefront\Page\GenericPageLoadedEvent;
use Shopware\Storefront\Page\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class ConfigurationSubscriberSpec extends ObjectBehavior
{
    public function let(
        Communication $communication,
        ExtensionConfig $extensionConfig,
        RouterInterface $router
    ) {
        $fieldRoles              = [];
        $communicationParameters = [];
        $communication->getServerUrl()->willReturn('https://factfinder.server.com');
        $extensionConfig->getTrackingSettings()->willReturn(
            [
                'addToCart' => [
                    'count' => 'count_as_one',
                ],
            ]
        );
        $extensionConfig->getRedirectMapping()->willReturn(new RedirectMapping(''));
        $this->beConstructedWith($communication, $extensionConfig, $router, $fieldRoles, $communicationParameters);
    }

    public function it_will_return_factfinderchannel_for_specific_sales_channel_id(
        Communication          $communication,
        GenericPageLoadedEvent $event,
        SalesChannelContext    $salesChannelContext,
        SalesChannelEntity     $salesChannel,
        CustomerEntity         $customer,
        CurrencyEntity         $currency,
        Request                $request,
        Page                   $page
    ) {
        $event->getSalesChannelContext()->willReturn($salesChannelContext);
        $salesChannelContext->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);
        $salesChannelContext->getCurrency()->willReturn($currency);
        $currency->getIsoCode()->willReturn('EUR');
        $salesChannelContext->getSalesChannel()->willReturn($salesChannel);
        $salesChannel->getId()->willReturn('main_sales_channel');
        $communication->getVersion()->willReturn(Version::NG);
        $communication->getApiVersion()->willReturn('v5');
        $communication->isSsrActive()->willReturn(false);
        $communication->isProxyEnabled()->willReturn(false);
        $communication->getChannel('main_sales_channel')->willReturn('some_ff_channel');
        $communication->getFieldRoles(Argument::any())->willReturn([]);
        $event->getRequest()->willReturn($request);
        $request->get('_route', Argument::any())->willReturn('factfinder');
        $request->getLocale()->willReturn('en');
        $request->isXmlHttpRequest()->willReturn(false);
        $event->getPage()->willReturn($page);

        $page->hasExtension('factfinder')->willReturn(false);
        $page->addExtension(
            'factfinder',
            new LogicalAndToken(
                [
                    new TypeToken(ArrayEntity::class),
                    Argument::withEntry('communication', Argument::withEntry('channel', 'some_ff_channel'))
                ]
            ))->shouldBeCalled();

        $this->onPageLoaded($event);
    }

    public function it_will_add_page_extension_for_storefront_render_event(
        Communication          $communication,
        StorefrontRenderEvent  $event,
        SalesChannelContext    $salesChannelContext,
        SalesChannelEntity     $salesChannel,
        CustomerEntity         $customer,
        CurrencyEntity         $currency,
        Request                $request,
        Struct                 $extension,
        Page                   $page
    ) {
        $event->getSalesChannelContext()->willReturn($salesChannelContext);
        $salesChannelContext->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);
        $salesChannelContext->getCurrency()->willReturn($currency);
        $currency->getIsoCode()->willReturn('EUR');
        $salesChannelContext->getSalesChannel()->willReturn($salesChannel);
        $salesChannel->getId()->willReturn('main_sales_channel');
        $communication->getChannel('main_sales_channel')->willReturn('some_ff_channel');
        $communication->getFieldRoles(Argument::any())->willReturn([]);
        $communication->getVersion()->willReturn(Version::NG);
        $communication->getApiVersion()->willReturn('v5');
        $communication->isSsrActive()->willReturn(false);
        $communication->isProxyEnabled()->willReturn(false);
        $event->getRequest()->willReturn($request);
        $request->get('_route', Argument::any())->willReturn('factfinder');
        $request->getLocale()->willReturn('en');
        $request->isXmlHttpRequest()->willReturn(false);
        $event->getParameters()->willReturn(['page' => $page]);

        $page->hasExtension('factfinder')->willReturn(false);
        $page->addExtension(
            'factfinder',
            new LogicalAndToken(
                [
                    new TypeToken(ArrayEntity::class),
                    Argument::withEntry('communication', Argument::withEntry('channel', 'some_ff_channel'))
                ]
            ))->shouldBeCalled();

        $this->onPageLoaded($event);
    }

    public function it_will_throw_exception_when_event_does_not_have_page(
        Communication          $communication,
        StorefrontRenderEvent  $event,
        SalesChannelContext    $salesChannelContext,
        SalesChannelEntity     $salesChannel,
        CustomerEntity         $customer,
        CurrencyEntity         $currency,
        Request                $request
    ) {
        $event->getSalesChannelContext()->willReturn($salesChannelContext);
        $salesChannelContext->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);
        $salesChannelContext->getCurrency()->willReturn($currency);
        $currency->getIsoCode()->willReturn('EUR');
        $salesChannelContext->getSalesChannel()->willReturn($salesChannel);
        $salesChannel->getId()->willReturn('main_sales_channel');
        $communication->getChannel('main_sales_channel')->willReturn('some_ff_channel');
        $communication->getFieldRoles(Argument::any())->willReturn([]);
        $communication->getVersion()->willReturn(Version::NG);
        $communication->getApiVersion()->willReturn('v5');
        $communication->isSsrActive()->willReturn(false);
        $communication->isProxyEnabled()->willReturn(false);
        $event->getRequest()->willReturn($request);
        $request->get('_route', Argument::any())->willReturn('factfinder');
        $request->getLocale()->willReturn('en');
        $request->isXmlHttpRequest()->willReturn(false);
        $event->getParameters()->willReturn([]);
        $this->onPageLoaded($event);
        $this->shouldThrow(new Exception(sprintf('Unable to get page from event %s.', get_class($event->getWrappedObject()))));
    }
}
