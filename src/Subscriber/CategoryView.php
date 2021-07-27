<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Export\Field\CategoryPath;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Category\SalesChannel\AbstractCategoryRoute;
use Shopware\Storefront\Page\Navigation\NavigationPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CategoryView implements EventSubscriberInterface
{
    private AbstractCategoryRoute $cmsPageRoute;

    private CategoryPath $categoryPath;

    private string $fieldName;

    private array $initial;

    public function __construct(
        AbstractCategoryRoute $cmsPageRoute,
        CategoryPath $categoryPath,
        string $categoryPathFieldName,
        array $initialNavigationParams = []
    ) {
        $this->cmsPageRoute = $cmsPageRoute;
        $this->categoryPath = $categoryPath;
        $this->fieldName    = $categoryPathFieldName;
        $this->initial      = $initialNavigationParams;
    }

    public static function getSubscribedEvents()
    {
        return [NavigationPageLoadedEvent::class => 'onPageLoaded'];
    }

    public function onPageLoaded(NavigationPageLoadedEvent $event): void
    {
        $navigationId = $event->getRequest()->get('navigationId', $event->getSalesChannelContext()->getSalesChannel()->getNavigationCategoryId());
        $category     = $this->cmsPageRoute->load($navigationId, $event->getRequest(), $event->getSalesChannelContext())->getCategory();
        $path         = $this->getPath($category);
        $event->getPage()->getExtension('factfinder')->assign(
            [
                'communication' => [
                    'search-immediate' => 'true',
                    'add-params'       => $path ? implode(',', $this->initial + [sprintf('filter=%s', urlencode($this->fieldName . ':' . $path))]) : '',
                ],
            ]);
    }

    private function getPath(CategoryEntity $category): string
    {
        return implode('/', array_map('rawurlencode', array_slice($category->getBreadcrumb(), 1)));
    }
}
