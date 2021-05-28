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
    /** @var AbstractCategoryRoute */
    private $cmsPageRoute;

    /** @var CategoryPath */
    private $categoryPath;

    /** @var string */
    private $fieldName;

    /** @var array */
    private $initial;

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
        $event->getPage()->getExtension('factfinder')->assign(
            [
                'communication' => [
                    'search-immediate' => 'true',
                    'add-params'       => implode(',', $this->initial + [sprintf('filter=%s', urlencode($this->fieldName . ':' . $this->getPath($category)))]),
                ],
            ]);
    }

    private function getPath(CategoryEntity $category): string
    {
        return implode('/', array_map('rawurlencode', array_slice($category->getBreadcrumb(), 1)));
    }
}
