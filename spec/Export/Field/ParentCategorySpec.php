<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use PhpSpec\Wrapper\Collaborator;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Shopware\Core\Content\Category\Service\CategoryBreadcrumbBuilder;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class ParentCategorySpec extends ObjectBehavior
{
    /** @var CategoryBreadcrumbBuilder|Collaborator */
    private Collaborator $breadcrumbBuilder;

    /** @var SalesChannelEntity|Collaborator */
    private Collaborator $salesChannelEntity;

    public function let(
        SalesChannelService $channelService,
        CategoryBreadcrumbBuilder $breadcrumbBuilder,
        SalesChannelContext $salesChannelContext,
        SalesChannelEntity $salesChannelEntity
    ) {
        $this->breadcrumbBuilder = $breadcrumbBuilder;
        $this->salesChannelEntity = $salesChannelEntity;
        $channelService->getSalesChannelContext()->willReturn($salesChannelContext);
        $salesChannelContext->getSalesChannel()->willReturn($salesChannelEntity);
        $this->beConstructedWith($channelService, $breadcrumbBuilder);
    }

    public function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    public function it_should_export_parent_category_name(Category $category)
    {
        //order might be mixed
        $categoryId      = '3';
        $plainBreadcrumb = [
            '2'         => 'Electronics',
            '1'         => 'Home',
            $categoryId => 'Laptops'
        ];
        $this->breadcrumbBuilder->build($category, $this->salesChannelEntity, $categoryId)->willReturn(['Electronics', 'Laptops']);
        $category->getPlainBreadcrumb()->willReturn($plainBreadcrumb);
        $category->getParentId()->willReturn($categoryId);
        $this->salesChannelEntity->getNavigationCategoryId()->willReturn($categoryId);

        $this->getValue($category)->shouldReturn('Electronics');
    }

    public function it_should_export_empty_string_if_category_has_no_parent(Category $category)
    {
        $category->getPlainBreadcrumb()->willReturn([]);
        $category->getParentId()->willReturn(null);
        $this->salesChannelEntity->getNavigationCategoryId()->willReturn('');

        $this->getValue($category)->shouldReturn("");
    }

    public function it_should_return_name_written_in_lower_case()
    {
        $this->getName()->shouldReturn('parentCategory');
    }
}
