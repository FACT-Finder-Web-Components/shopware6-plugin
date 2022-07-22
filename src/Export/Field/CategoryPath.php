<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Shopware\Core\Content\Category\Service\CategoryBreadcrumbBuilder;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
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
        if ($entity->getCategories() === null) {
            return '';
        }

        return implode('|', $entity->getCategories()->fmap($this->createPath($this->channelService->getSalesChannelContext()->getSalesChannel())));
    }

    public function getCompatibleEntityTypes(): array
    {
        return [SalesChannelProductEntity::class];
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
}
