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

class CategoryPageSubscriber implements EventSubscriberInterface
{
    private AbstractCategoryRoute $cmsPageRoute;

    private CategoryPath $categoryPath;

    private string $fieldName;

    private array $addParams;

    public function __construct(
        AbstractCategoryRoute $cmsPageRoute,
        CategoryPath $categoryPath,
        string $categoryPathFieldName,
        array $categoryPageAddParams = []
    ) {
        $this->cmsPageRoute = $cmsPageRoute;
        $this->categoryPath = $categoryPath;
        $this->fieldName    = $categoryPathFieldName;
        $this->addParams    = $categoryPageAddParams;
    }

    public static function getSubscribedEvents()
    {
        return [NavigationPageLoadedEvent::class => 'onPageLoaded'];
    }

    public function onPageLoaded(NavigationPageLoadedEvent $event): void
    {
        $navigationId = $event->getRequest()->get('navigationId', $event->getSalesChannelContext()->getSalesChannel()->getNavigationCategoryId());
        $category     = $this->cmsPageRoute->load($navigationId, $event->getRequest(), $event->getSalesChannelContext())->getCategory();

        $disableImmediate = safeGetByName($category->getCustomFields(), OmikronFactFinder::DISABLE_SEARCH_IMMEDIATE_CUSTOM_FIELD_NAME);
        $isHome           = $event->getRequest()->get('_route') === 'frontend.home.page';
        $searchImmediate  = !$isHome && !$disableImmediate;

        $baseAddParams = array_filter(explode(',', (string) safeGetByName($event->getPage()->getExtension('factfinder')->get('communication'), 'add-params')));
        /**
         * $this->addParams + $baseAddParams will not override entries from $baseAddParams as $this->addParams is associative array and $baseAddParams is not
         * $this->addParams is associative array because this is how parameters collection is passed as an argument constructor in Symfony.
         */
        $mergedAddParams = array_reduce($this->addParams + $baseAddParams, function (array $acc, string $expr): array {
            list($key, $value) = explode('=', $expr);
            return $acc + [$key => $value];
        }, []);

        $communication = [
                'add-params'       => implode(',', array_map(fn (string $key, string $value): string => sprintf('%s=%s', $key, $value), array_keys($mergedAddParams), array_values($mergedAddParams))),
            ] + ($searchImmediate ? ['category-page' => $this->prepareCategoryPath($category)] : []);

        $event->getRequest()->attributes->set('isCategoryPage', true);
        $event->getPage()->getExtension('factfinder')->assign(
            [
                'communication'         => $communication,
                'searchImmediate'       => $searchImmediate ? 'true' : 'false',
                'categoryPathFieldName' => "{$this->fieldName}ROOT",
            ]
        );
    }

    private function prepareCategoryPath(CategoryEntity $categoryEntity): string
    {
        $categories   = array_slice($categoryEntity->getBreadcrumb(), 1);
        $categoryPath = implode('/', array_map(fn ($category): string => $this->encodeCategoryName($category), $categories));

        return sprintf('filter=%s', urlencode($this->fieldName . ':' . $categoryPath));
    }

    private function encodeCategoryName(string $path): string
    {
        //important! do not modify this method
        return preg_replace('/\+/', '%2B', preg_replace('/\//', '%2F',
            preg_replace('/%/', '%25', $path)));
    }
}
