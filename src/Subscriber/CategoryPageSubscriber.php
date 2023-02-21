<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Config\ExtensionConfig;
use Omikron\FactFinder\Shopware6\OmikronFactFinder;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Field\CategoryPath;
use Shopware\Core\Content\Category\SalesChannel\AbstractCategoryRoute;
use Shopware\Storefront\Page\Navigation\NavigationPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function Omikron\FactFinder\Shopware6\Internal\Utils\safeGetByName;

class CategoryPageSubscriber implements EventSubscriberInterface
{
    private AbstractCategoryRoute $cmsPageRoute;

    private Communication $config;

    private ExtensionConfig $extensionConfig;

    private string $fieldName;

    private array $addParams;

    public function __construct(
        AbstractCategoryRoute $cmsPageRoute,
        Communication $config,
        ExtensionConfig $extensionConfig,
        string $categoryPathFieldName,
        array $categoryPageAddParams = []
    ) {
        $this->cmsPageRoute    = $cmsPageRoute;
        $this->config          = $config;
        $this->extensionConfig = $extensionConfig;
        $this->fieldName       = $categoryPathFieldName;
        $this->addParams       = $categoryPageAddParams;
    }

    public static function getSubscribedEvents()
    {
        return [NavigationPageLoadedEvent::class => 'onPageLoaded'];
    }

    public function onPageLoaded(NavigationPageLoadedEvent $event): void
    {
        $navigationId = $event->getRequest()->get('navigationId', $event->getSalesChannelContext()->getSalesChannel()->getNavigationCategoryId());
        $category     = $this->cmsPageRoute->load($navigationId, $event->getRequest(), $event->getSalesChannelContext())->getCategory();
        $route        = $event->getRequest()->get('_route');

        $disableImmediate = safeGetByName($category->getCustomFields(), OmikronFactFinder::DISABLE_SEARCH_IMMEDIATE_CUSTOM_FIELD_NAME);
        $isHome           = $route === 'frontend.home.page';
        $isCategory       = !$isHome && !$disableImmediate;
        $searchImmediate  = $this->config->isSsrActive() === false && $isCategory;

        $baseAddParams = array_filter(explode(',', (string) safeGetByName($event->getPage()->getExtension('factfinder')->get('communication'), 'add-params')));
        /**
         * $this->addParams + $baseAddParams will not override entries from $baseAddParams as $this->addParams is associative array and $baseAddParams is not
         * $this->addParams is associative array because this is how parameters collection is passed as an argument constructor in Symfony.
         */
        $mergedAddParams = array_reduce($this->addParams + $baseAddParams, function (array $acc, string $expr): array {
            list($key, $value) = explode('=', $expr);
            return $acc + [$key => $value];
        }, []);

        $categoryPath  = (new CategoryPath($this->fieldName))->getValue($category);
        $communication = [
                'add-params'       => implode(',', array_map(fn (string $key, string $value): string => sprintf('%s=%s', $key, $value), array_keys($mergedAddParams), array_values($mergedAddParams))),
            ] + ($isCategory ? ['category-page' => $categoryPath] : []);

        if ($route === 'frontend.navigation.page') {
            $event->getRequest()->attributes->set('categoryPath', $categoryPath);
        }

        $event->getPage()->getExtension('factfinder')->assign(
            [
                'communication'         => $communication,
                'trackingSettings'      => $this->extensionConfig->getTrackingSettings(),
                'redirectMapping'       => (string) $this->extensionConfig->getRedirectMapping(),
                'searchImmediate'       => $searchImmediate ? 'true' : 'false',
                'categoryPathFieldName' => "{$this->fieldName}ROOT",
            ]
        );
    }
}
