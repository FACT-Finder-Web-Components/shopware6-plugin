<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Data\Entity\CategoryEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Shopware\Core\Content\Category\CategoryEntity as ShopwareCategoryEntity;
use Shopware\Core\Content\Category\Service\CategoryBreadcrumbBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class CategoryPath implements FieldInterface
{
    private string $fieldName;
    private SalesChannelService $channelService;
    private CategoryBreadcrumbBuilder $breadcrumbBuilder;

    public function __construct(
        SalesChannelService $channelService,
        CategoryBreadcrumbBuilder $breadcrumbBuilder,
        string $categoryPathFieldName
    ) {
        $this->fieldName         = $categoryPathFieldName;
        $this->channelService    = $channelService;
        $this->breadcrumbBuilder = $breadcrumbBuilder;
    }

    public function getName(): string
    {
        return $this->fieldName;
    }

    public function getValue(Entity $entity): string
    {
        if ($entity->getCategories($entity) === null) {
            return '';
        }

        return implode('|', $this->getCategories($entity)->fmap($this->createPath($this->channelService->getSalesChannelContext()->getSalesChannel())));
    }

    public function getCompatibleEntityTypes(): array
    {
        return [ProductEntity::class, CategoryEntity::class];
    }

    private function createPath(SalesChannelEntity $salesChannel): callable
    {
        return function (Category $category) use ($salesChannel) {
            $breadcrumb = $this->breadcrumbBuilder->build($category, $salesChannel, $salesChannel->getNavigationCategoryId()) ?? [];
            return in_array($salesChannel->getNavigationCategoryId(), array_keys($category->getPlainBreadcrumb()))
                ? implode('/', array_map('urlencode', $breadcrumb))
                : '';
        };
    }

    private function getCategories(Entity $entity): CategoryCollection
    {
        if ($entity instanceof ShopwareCategoryEntity) {
            return new CategoryCollection([$entity]);
        }

        return $entity->getCategories();
    }
}
