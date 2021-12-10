<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Export\Field\CategoryPath;
use Omikron\FactFinder\Shopware6\OmikronFactFinder;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Category\SalesChannel\AbstractCategoryRoute;
use Shopware\Storefront\Page\Navigation\NavigationPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function Omikron\FactFinder\Shopware6\Internal\Utils\safeGetByName;

class CategoryView implements EventSubscriberInterface
{
    private AbstractCategoryRoute $cmsPageRoute;

    private CategoryPath $categoryPath;

    private string $fieldName;

    private array $addParams;

    public function __construct(
        AbstractCategoryRoute $cmsPageRoute,
        CategoryPath $categoryPath,
        string $categoryPathFieldName,
        array $initialNavigationParams = []
    ) {
        $this->cmsPageRoute = $cmsPageRoute;
        $this->categoryPath = $categoryPath;
        $this->fieldName    = $categoryPathFieldName;
        $this->addParams    = $initialNavigationParams;
    }

    public static function getSubscribedEvents()
    {
        return [NavigationPageLoadedEvent::class => 'onPageLoaded'];
    }

    public function onPageLoaded(NavigationPageLoadedEvent $event): void
    {
        $navigationId     = $event->getRequest()->get('navigationId', $event->getSalesChannelContext()->getSalesChannel()->getNavigationCategoryId());
        $category         = $this->cmsPageRoute->load($navigationId, $event->getRequest(), $event->getSalesChannelContext())->getCategory();

        $disableImmediate = safeGetByName($category->getCustomFields())(OmikronFactFinder::DISABLE_SEARCH_IMMEDIATE_CUSTOM_FIELD_NAME);
        $isHome           = $event->getRequest()->get('_route') === 'frontend.home.page';
        $isCategory       = !$isHome && !$disableImmediate;

        $categoryPath     = $this->getPath($this->cmsPageRoute->load($navigationId, $event->getRequest(), $event->getSalesChannelContext())->getCategory());

        $communication = [
                'search-immediate' => !$isHome && !$disableImmediate ? 'true' : 'false',
                'add-params'       => implode(',', $this->addParams),
            ] + ($isCategory ? ['category-page' => $this->prepareCategoryPath($categoryPath)] : []);

        $event->getPage()->getExtension('factfinder')->assign(['communication' => $communication]);
    }

    private function prepareCategoryPath($path): string
    {
        $filterValue = is_int(strpos($this->fieldName, 'CategoryPath')) ? urlencode($this->fieldName . ':' . $path) : '\'\'';
        return sprintf('filter=%s', $filterValue);
    }

    private function getPath(CategoryEntity $category): string
    {
        return implode('/', array_map('rawurlencode', array_slice($category->getBreadcrumb(), 1)));
    }
}
