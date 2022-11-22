<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Config\Communication;
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

class ConfigurationSubscriberSpec extends ObjectBehavior
{
    function let(Communication $communication)
    {
        $fieldRoles              = [];
        $communicationParameters = [];
        $communication->getServerUrl()->willReturn('https://factfinder.server.com');
        $this->beConstructedWith($communication, $fieldRoles, $communicationParameters);
    }

    function it_will_return_factfinderchannel_for_specific_sales_channel_id(
        Communication          $communication,
        GenericPageLoadedEvent $event,
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
        $event->getRequest()->willReturn($request);
        $request->get('_route')->willReturn('factfinder');
        $request->getLocale()->willReturn('en');
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

    function it_will_add_page_extension_for_storefront_render_event(
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
        $event->getRequest()->willReturn($request);
        $request->get('_route')->willReturn('factfinder');
        $request->getLocale()->willReturn('en');
        $event->getParameters()->willReturn(['page' => $page]);

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
}
