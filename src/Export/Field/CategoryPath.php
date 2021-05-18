<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Shopware\Core\Content\Category\Service\CategoryBreadcrumbBuilder;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class CategoryPath implements FieldInterface
{
    /** @var string */
    private $fieldName;

    /** @var SalesChannelService */
    private $channelService;

    /** @var CategoryBreadcrumbBuilder */
    private $breadcrumbBuilder;

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

    public function getValue(Product $product): string
    {
        $salesChannel = $this->channelService->getSalesChannelContext()->getSalesChannel();

        return implode('|', $product->getCategories()->fmap(function (Category $category) use ($salesChannel): string {
            $breadcrumb = $this->breadcrumbBuilder->build($category, $salesChannel, $salesChannel->getNavigationCategoryId()) ?? [];
            return in_array($salesChannel->getNavigationCategoryId(), array_keys($category->getPlainBreadcrumb())) ? implode('/', array_map('urlencode', $breadcrumb)) : '';
        }));
    }
}
