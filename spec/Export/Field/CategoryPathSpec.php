<?php

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Category\Service\CategoryBreadcrumbBuilder;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class CategoryPathSpec extends ObjectBehavior
{
    function let(
        SalesChannelService $salesChannelService,
        SalesChannelContext $channelContext,
        CategoryBreadcrumbBuilder $breadcrumbBuilder
    ) {
        $channelContext->getSalesChannel()->willReturn($this->getSalesChannel('home'));
        $salesChannelService->getSalesChannelContext()->willReturn($channelContext);
        $this->beConstructedWith($salesChannelService, $breadcrumbBuilder, 'CategoryPath');
    }

    function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('CategoryPath');
    }

    function it_should_create_correct_path_if_product_is_assigned_to_multiple_categories(
        Product $product,
        CategoryBreadcrumbBuilder $breadcrumbBuilder,
        SalesChannelService $salesChannelService,
        SalesChannelContext $channelContext,
        SalesChannelEntity $salesChannel
    ) {
        $categories = $this->prepareCategoryCollection();
        $channelContext->getSalesChannel()->willReturn($this->getSalesChannel('home'));
        $salesChannelService->getSalesChannelContext()->willReturn($channelContext);
        $product->getCategories()->willReturn(new CategoryCollection([$categories[3], $categories[5]]));

        $breadcrumbBuilder->build(Argument::which('getId', 'id3'), Argument::type(SalesChannelEntity::class), 'home')->willReturn(
            [
                'id1' => 'Category1-1',
                'id2' => 'Category1-2',
                'id3' => 'Category1-3',
            ]);

        $breadcrumbBuilder->build(Argument::which('getId', 'id5'), Argument::type(SalesChannelEntity::class), 'home')->willReturn(
            [
                'id4' => 'Category2-1',
                'id5' => 'Category2-2',
            ]);


        $this->getValue($product)->shouldReturn('Category1-1/Category1-2/Category1-3|Category2-1/Category2-2');
    }

    function it_should_filter_out_categories_from_not_active_sales_channel(
        Product $product,
        CategoryBreadcrumbBuilder $breadcrumbBuilder,
        SalesChannelService $salesChannelService,
        SalesChannelContext $channelContext,
        SalesChannelEntity $salesChannel
    ) {
        $categories = $this->prepareCategoryCollection();
        $channelContext->getSalesChannel()->willReturn($this->getSalesChannel('another-home'));
        $salesChannelService->getSalesChannelContext()->willReturn($channelContext);
        $product->getCategories()->willReturn(new CategoryCollection([$categories[3], $categories[8]]));

        $breadcrumbBuilder->build(Argument::which('getId', 'id3'), Argument::type(SalesChannelEntity::class), 'another-home')->willReturn(
            [
                'id1' => 'Category1-1',
                'id2' => 'Category1-2',
                'id3' => 'Category1-3',
            ]);

        $breadcrumbBuilder->build(Argument::which('getId', 'id8'), Argument::type(SalesChannelEntity::class), 'another-home')->willReturn(
            [
                'id7' => 'Category3-1',
                'id8' => 'Category3-2',
            ]);

        $this->getValue($product)->shouldReturn('Category3-1/Category3-2');
    }

    private function prepareCategoryCollection(): array
    {
        $categoriesData = [
            [
                'name'            => 'Home',
                'id'              => 'home',
                'path'            => '||',
                'plainBreadcrumb' => [
                    'home' => 'Home',
                ]
            ],
            [
                'name'            => 'Category1-1',
                'id'              => 'id1',
                'path'            => '|home|',
                'plainBreadcrumb' => [
                    'home' => 'Home',
                    'id1'  => 'Category1-1',
                ]
            ],
            [
                'name'            => 'Category1-2',
                'id'              => 'id2',
                'path'            => '|home|id1|',
                'plainBreadcrumb' => [
                    'home' => 'Home',
                    'id1'  => 'Category1-1',
                    'id2'  => 'Category1-2',
                ]
            ],
            [
                'name'            => 'Category1-3',
                'id'              => 'id3',
                'path'            => '|home|id1|id2|',
                'plainBreadcrumb' => [
                    'home' => 'Home',
                    'id1'  => 'Category1-2',
                    'id2'  => 'Category1-2',
                    'id3'  => 'Category1-3',
                ]
            ],
            [
                'name'            => 'Category2-1',
                'id'              => 'id4',
                'path'            => '|home|',
                'plainBreadcrumb' => [
                    'home' => 'Home',
                    'id4'  => 'Category2-1',
                ]
            ],
            [
                'name'            => 'Category2-2',
                'id'              => 'id5',
                'path'            => '|home|id4|',
                'plainBreadcrumb' => [
                    'home' => 'Home',
                    'id4'  => 'Category2-1',
                    'id5'  => 'Category2-2',
                ]
            ],
            [
                'name'            => 'Another Home',
                'id'              => 'id6',
                'path'            => '||',
                'plainBreadcrumb' => [
                    'another-home' => 'Another Home',
                    'id6'          => 'Category3-1',
                ]
            ],
            [
                'name'            => 'Category3-1',
                'id'              => 'id7',
                'path'            => '|another-home|',
                'plainBreadcrumb' => [
                    'another-home' => 'Another Home',
                    'id7'          => 'Category3-1',
                ]
            ],
            [
                'name'            => 'Category3-2',
                'id'              => 'id8',
                'path'            => '|another-home|id7|',
                'plainBreadcrumb' => [
                    'another-home' => 'Another Home',
                    'id7'          => 'Category3-1',
                    'id8'          => 'Category3-2',
                ]
            ],
        ];

        return array_map(function (array $categoryData) {
            $categoryEntity = new CategoryEntity();
            $categoryEntity->setTranslated(['name' => $categoryData['name']]);
            $categoryEntity->setId($categoryData['id']);
            $categoryEntity->setPath($categoryData['path']);
            $categoryEntity->setTranslated(['breadcrumb' => $categoryData['plainBreadcrumb']]);
            return $categoryEntity;
        }, $categoriesData);
    }

    private function getSalesChannel(string $navigationCategoryId): SalesChannelEntity
    {
        $salesChannel = new SalesChannelEntity();
        $salesChannel->setNavigationCategoryId($navigationCategoryId);
        return $salesChannel;
    }
}
