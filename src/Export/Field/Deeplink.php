<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Closure;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\CategoryEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\CmsPageEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity;
use Shopware\Core\Content\Seo\Event\SeoUrlUpdateEvent;
use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Storefront\Framework\Seo\SeoUrlRoute\NavigationPageSeoUrlRoute as CategoryRoute;
use Shopware\Storefront\Framework\Seo\SeoUrlRoute\ProductPageSeoUrlRoute as ProductRoute;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function Omikron\FactFinder\Shopware6\Internal\Utils\first;
use function Omikron\FactFinder\Shopware6\Internal\Utils\safeGetByName;

class Deeplink implements FieldInterface, EventSubscriberInterface
{
    private SeoUrlUpdater $seoUrlUpdater;
    private Closure $fetchCallback;

    public function __construct(SeoUrlUpdater $seoUrlUpdater)
    {
        $this->seoUrlUpdater = $seoUrlUpdater;
    }

    public function getName(): string
    {
        return 'Deeplink';
    }

    public static function getSubscribedEvents()
    {
        return [SeoUrlUpdateEvent::class => 'onUrlUpdated'];
    }

    public function getValue(Entity $entity): string
    {
        if ($entity->getSeoUrls() === null) {
            return '';
        }

        $url                = $entity->getSeoUrls()->first();
        $getSeoUrlRouteName = fn (Entity $entity) => $entity instanceof ProductEntity ? ProductRoute::ROUTE_NAME : CategoryRoute::ROUTE_NAME;
        $formUrl            = fn (string $url): string => $url ? '/' . ltrim($url, '/') : '';

        if (!$url) {
            $this->seoUrlUpdater->update($getSeoUrlRouteName($entity), [$entity->getId()]);
            $seoUrls = call_user_func($this->fetchCallback);
            return $formUrl((string) safeGetByName($seoUrls, 'seoPathInfo'));
        }
        return $formUrl($url->getSeoPathInfo());
    }

    public function onUrlUpdated(SeoUrlUpdateEvent $event): void
    {
        //@todo second argument could be removed?
        $this->fetchCallback = fn (): array => first($event->getSeoUrls(), []);
    }

    public function getCompatibleEntityTypes(): array
    {
        return [ProductEntity::class, CmsPageEntity::class, CategoryEntity::class];
    }
}
