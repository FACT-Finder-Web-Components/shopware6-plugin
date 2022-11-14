<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Category\CategoryEntity as Category;

class ParentCategorySpec extends ObjectBehavior
{
    public function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    public function it_should_export_parent_category_name(Category $category)
    {
        //order might be mixed
        $parentId        = '3';
        $plainBreadcrumb = [
            '2'       => 'Electronics',
            '1'       => 'Home',
            $parentId => 'Laptops'
        ];
        $category->getPlainBreadcrumb()->willReturn($plainBreadcrumb);
        $category->getParentId()->willReturn($parentId);

        $this->getValue($category)->shouldReturn('Laptops');
    }

    public function it_should_export_empty_string_if_category_has_no_parent(Category $category)
    {
        $category->getPlainBreadcrumb()->willReturn([]);
        $category->getParentId()->willReturn(null);

        $this->getValue($category)->shouldReturn("");
    }

    public function it_should_return_name_written_in_lower_case()
    {
        $this->getName()->shouldReturn('parentCategory');
    }
}
