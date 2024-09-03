<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Data\Entity\CategoryEntity;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Content\Category\Service\CategoryBreadcrumbBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class ParentCategory implements FieldInterface
{
    private SalesChannelService $channelService;
    private CategoryBreadcrumbBuilder $breadcrumbBuilder;

    public function __construct(
        SalesChannelService $channelService,
        CategoryBreadcrumbBuilder $breadcrumbBuilder,
    ) {
        $this->channelService    = $channelService;
        $this->breadcrumbBuilder = $breadcrumbBuilder;
    }

    public function getName(): string
    {
        return 'parentCategory';
    }

    public function getValue(Entity $entity): string
    {
        $salesChannel = $this->channelService->getSalesChannelContext()->getSalesChannel();
        $breadcrumbs  = $this->breadcrumbBuilder->build($entity, $salesChannel, $salesChannel->getNavigationCategoryId()) ?? [];
        array_pop($breadcrumbs);

        return implode('/', $breadcrumbs) ?? '';
    }

    public function getCompatibleEntityTypes(): array
    {
        return [CategoryEntity::class];
    }
}
