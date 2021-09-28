<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Export\Field\CategoryPath;
use Omikron\FactFinder\Shopware6\OmikronFactFinder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Category\SalesChannel\AbstractCategoryRoute;
use Shopware\Core\Content\Category\SalesChannel\CategoryRouteResponse;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Storefront\Page\Navigation\NavigationPage;
use Shopware\Storefront\Page\Navigation\NavigationPageLoadedEvent;
use Symfony\Component\HttpFoundation\Request;

class CategoryViewSpec extends ObjectBehavior
{
    public function let(
        AbstractCategoryRoute $cmsPageRoute,
        CategoryPath $categoryPath,
        NavigationPageLoadedEvent $event,
        Request $request,
        CategoryEntity $categoryEntity,
        NavigationPage $navigationPage,
        Struct $extension,
        SalesChannelContext $salesChannelContext,
        SalesChannelEntity $salesChannelEntity,
        CategoryRouteResponse $categoryRouteResponse)
    {
        $navigationId = '1';
        $event->getRequest()->willReturn($request);
        $event->getSalesChannelContext()->willReturn($salesChannelContext);
        $salesChannelContext->getSalesChannel()->willReturn($salesChannelEntity);
        $salesChannelEntity->getNavigationCategoryId()->willReturn($navigationId);
        $request->get('navigationId', $navigationId)->willReturn($navigationId);
        $cmsPageRoute->load('1', $request, $salesChannelContext)->willReturn($categoryRouteResponse);
        $categoryRouteResponse->getCategory()->willReturn($categoryEntity);
        $categoryEntity->getBreadcrumb()->willReturn(
            [
                0 => 'Home',
                1 => 'Books & Sports',
                2 => 'Home & Garden',
            ])->shouldBeCalled();
        $event->getPage()->willReturn($navigationPage);
        $navigationPage->getExtension('factfinder')->willReturn($extension);
        $this->beConstructedWith($cmsPageRoute, $categoryPath, 'CategoryPath, []');
    }

    public function it_will_not_add_search_immediate_attribute_if_its_disabled_in_category_config(
        CategoryEntity $categoryEntity,
        Struct $extension,
        NavigationPageLoadedEvent $event
    ) {
        $categoryEntity->getCustomFields()->willReturn([OmikronFactFinder::DISABLE_SEARCH_IMMEDIATE_CUSTOM_FIELD_NAME => false]);
        $extension->assign(Argument::withEntry('communication', Argument::withEntry('search-immediate', 'true')))->shouldBeCalled();
        $this->onPageLoaded($event);
    }

    public function it_will_add_search_immediate_attribute_if_its_enabled_in_category_config(
        CategoryEntity $categoryEntity,
        Struct $extension,
        NavigationPageLoadedEvent $event
    ) {
        $categoryEntity->getCustomFields()->willReturn([OmikronFactFinder::DISABLE_SEARCH_IMMEDIATE_CUSTOM_FIELD_NAME => true]);
        $extension->assign(Argument::withEntry('communication', Argument::withEntry('search-immediate', 'false')))->shouldBeCalled();
        $this->onPageLoaded($event);
    }

    public function it_will_not_fail_if_ff_cms_use_search_immediate_is_not_present_in_custom_fields(
        CategoryEntity $categoryEntity,
        Struct $extension,
        NavigationPageLoadedEvent $event
    ) {
        $categoryEntity->getCustomFields()->willReturn(null);
        $this->shouldNotThrow()->during('onPageLoaded', [$event]);
        $this->onPageLoaded($event);
    }

    public function it_will_encode_category_path_correctly(
        CategoryEntity $categoryEntity,
        Struct $extension,
        NavigationPageLoadedEvent $event
    ) {
        $categoryEntity->getCustomFields()->willReturn([]);
        $extension->assign(Argument::withEntry('communication', Argument::withEntry('add-params', Argument::containingString('filter=CategoryPath%2C+%5B%5D%3ABooks%2520%2526%2520Sports%2FHome%2520%2526%2520Garden'))))->shouldBeCalled();
        $this->onPageLoaded($event);
    }
}
