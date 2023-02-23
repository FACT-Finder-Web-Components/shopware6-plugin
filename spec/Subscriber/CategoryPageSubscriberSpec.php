<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Config\ExtensionConfig;
use Omikron\FactFinder\Shopware6\Domain\RedirectMapping;
use Omikron\FactFinder\Shopware6\OmikronFactFinder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Category\SalesChannel\AbstractCategoryRoute;
use Shopware\Core\Content\Category\SalesChannel\CategoryRouteResponse;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Storefront\Page\Navigation\NavigationPage;
use Shopware\Storefront\Page\Navigation\NavigationPageLoadedEvent;
use Symfony\Component\HttpFoundation\Request;

class CategoryPageSubscriberSpec extends ObjectBehavior
{
    private string $filterCategoryPath = 'filter=CategoryPath%3ABooks+%252B+Sports%2FHome+%252F+Garden+100%2525';

    public function let(
        AbstractCategoryRoute $cmsPageRoute,
        Communication $config,
        ExtensionConfig $extensionConfig,
        NavigationPageLoadedEvent $event,
        Request $request,
        CategoryEntity $categoryEntity,
        NavigationPage $navigationPage,
        ArrayEntity $extension,
        SalesChannelContext $salesChannelContext,
        SalesChannelEntity $salesChannelEntity,
        CategoryRouteResponse $categoryRouteResponse
    ) {
        $config->isSsrActive()->willReturn(false);
        $this->configure($cmsPageRoute, $config, $extensionConfig, $event, $request, $categoryEntity, $navigationPage, $extension, $salesChannelContext, $salesChannelEntity, $categoryRouteResponse);
    }

    public function it_should_add_category_page_attribute_in_category_config(
        CategoryEntity $categoryEntity,
        ArrayEntity $extension,
        NavigationPageLoadedEvent $event
    ) {
        $categoryEntity->getCustomFields()->willReturn([OmikronFactFinder::DISABLE_SEARCH_IMMEDIATE_CUSTOM_FIELD_NAME => false]);
        $extension->assign(Argument::withEntry('communication', Argument::withEntry('category-page', Argument::any())))->shouldBeCalled();
        $this->onPageLoaded($event);
    }

    public function it_should_not_add_category_page_attribute_in_category_config(
        CategoryEntity $categoryEntity,
        ArrayEntity $extension,
        NavigationPageLoadedEvent $event,
        Request $request
    ) {
        $request->get('_route')->willReturn('frontend.not_home.page');
        $event->getRequest()->willReturn($request);
        $categoryEntity->getCustomFields()->willReturn([OmikronFactFinder::DISABLE_SEARCH_IMMEDIATE_CUSTOM_FIELD_NAME => true]);
        $extension->assign(Argument::withEntry('communication', Argument::exact(['add-params' => 'navigation=true'])))->shouldBeCalled();
        $this->onPageLoaded($event);
    }

    public function it_should_not_fail_if_ff_cms_use_search_immediate_is_not_present_in_custom_fields(
        NavigationPageLoadedEvent $event,
        ArrayEntity $extension
    ) {
        $extension->assign(Argument::cetera())->shouldBeCalled();
        $this->shouldNotThrow()->during('onPageLoaded', [$event]);
    }

    public function it_should_encode_category_path_correctly(
        CategoryEntity $categoryEntity,
        ArrayEntity $extension,
        NavigationPageLoadedEvent $event
    ) {
        $extension->assign(Argument::withEntry('communication', Argument::withEntry('category-page', $this->filterCategoryPath)))->shouldBeCalled();
        $this->onPageLoaded($event);
    }

    public function it_should_not_add_category_path_to_add_params(
        CategoryEntity $categoryEntity,
        ArrayEntity $extension,
        NavigationPageLoadedEvent $event
    ) {
        $extension->assign(Argument::withEntry('communication', Argument::withEntry('add-params', Argument::not(Argument::containingString($this->filterCategoryPath)))))->shouldBeCalled();
        $this->onPageLoaded($event);
    }

    public function it_should_configure_add_params_if_set(
        CategoryEntity $categoryEntity,
        ArrayEntity $extension,
        NavigationPageLoadedEvent $event
    ) {
        $extension->assign(Argument::withEntry('communication', Argument::withEntry('add-params', 'navigation=true')))->shouldBeCalled();
        $this->onPageLoaded($event);
    }

    public function it_should_implode_multiple_add_params_correctly(
        AbstractCategoryRoute $cmsPageRoute,
        Communication $config,
        ExtensionConfig $extensionConfig,
        NavigationPageLoadedEvent $event,
        Request $request,
        CategoryEntity $categoryEntity,
        NavigationPage $navigationPage,
        ArrayEntity $extension,
        SalesChannelContext $salesChannelContext,
        SalesChannelEntity $salesChannelEntity,
        CategoryRouteResponse $categoryRouteResponse
    ) {
        $this->configure($cmsPageRoute, $config, $extensionConfig, $event, $request, $categoryEntity, $navigationPage, $extension, $salesChannelContext, $salesChannelEntity, $categoryRouteResponse, ['param1' => 'navigation=true', 'param2' =>'filterCustom=customValue']);
        $categoryEntity->getCustomFields()->willReturn([]);
        $extension->assign(Argument::withEntry('communication', Argument::withEntry('add-params', 'navigation=true,filterCustom=customValue')))->shouldBeCalled();
        $this->onPageLoaded($event);
    }

    public function it_should_merge_add_params_from_the_configuration_subscriber(
        AbstractCategoryRoute $cmsPageRoute,
        Communication $config,
        ExtensionConfig $extensionConfig,
        NavigationPageLoadedEvent $event,
        Request $request,
        CategoryEntity $categoryEntity,
        NavigationPage $navigationPage,
        ArrayEntity $extension,
        SalesChannelContext $salesChannelContext,
        SalesChannelEntity $salesChannelEntity,
        CategoryRouteResponse $categoryRouteResponse
    ) {
        $baseAddParams     = 'configurationParam=baseValue,configurationParam2=baseValue';
        $categoryAddParams = ['param1'=>'configurationParam=overridden', 'param2'=>'categoryParam=value1'];

        $this->configure($cmsPageRoute, $config, $extensionConfig, $event, $request, $categoryEntity, $navigationPage, $extension, $salesChannelContext, $salesChannelEntity, $categoryRouteResponse, $categoryAddParams);
        $extension->get('communication')->willReturn(['add-params' => $baseAddParams]);

        $extension->assign(Argument::withEntry('communication', Argument::withEntry('add-params', 'configurationParam=overridden,categoryParam=value1,configurationParam2=baseValue')))->shouldBeCalled();
        $this->onPageLoaded($event);
    }

    /**
     * This pseudo constructor is added to give possibility to specify different add-params for specific test cases.
     */
    private function configure(
        AbstractCategoryRoute $cmsPageRoute,
        Communication $config,
        ExtensionConfig $extensionConfig,
        NavigationPageLoadedEvent $event,
        Request $request,
        CategoryEntity $categoryEntity,
        NavigationPage $navigationPage,
        ArrayEntity $extension,
        SalesChannelContext $salesChannelContext,
        SalesChannelEntity $salesChannelEntity,
        CategoryRouteResponse $categoryRouteResponse,
        array $addParams = ['param1' =>'navigation=true'] //addParams parameters collections are passed as associative array (see comment src/Subscriber/CategoryPageSubscriber.php:55)
    ) {
        $navigationId = '1';
        $extensionConfig->getTrackingSettings()->willReturn(
            [
                'addToCart' => [
                    'count' => 'count_as_one',
                ],
            ]
        );
        $extensionConfig->getRedirectMapping()->willReturn(new RedirectMapping(''));
        $categoryEntity->getCustomFields()->willReturn(null);
        $event->getRequest()->willReturn($request);
        $event->getSalesChannelContext()->willReturn($salesChannelContext);
        $salesChannelContext->getSalesChannel()->willReturn($salesChannelEntity);
        $salesChannelEntity->getNavigationCategoryId()->willReturn($navigationId);
        $request->get('navigationId', $navigationId)->willReturn($navigationId);
        $request->get('_route')->willReturn('');
        $cmsPageRoute->load('1', $request, $salesChannelContext)->willReturn($categoryRouteResponse);
        $categoryRouteResponse->getCategory()->willReturn($categoryEntity);
        $categoryEntity->getBreadcrumb()->willReturn(
            [
                0 => 'Home',
                1 => 'Books + Sports',
                2 => 'Home / Garden 100%',
            ]);
        $event->getPage()->willReturn($navigationPage);
        $extension->get('communication')->willReturn([]);
        $navigationPage->getExtension('factfinder')->willReturn($extension);
        $this->beConstructedWith($cmsPageRoute, $config, $extensionConfig, 'CategoryPath', $addParams);
    }
}
