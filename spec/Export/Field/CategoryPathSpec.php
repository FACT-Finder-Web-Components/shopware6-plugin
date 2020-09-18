<?php

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class CategoryPathSpec extends ObjectBehavior
{
    function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('CategoryPath');
    }

    function it_should_create_correct_path_if_product_is_assigned_to_multiple_categories(Product $product)
    {
        $categories = $this->prepareCategoryCollection();
        $product->getCategories()->willReturn(new CategoryCollection(array_slice($categories, 0, 2)));
        $product->getCategoriesRo()->willReturn(new CategoryCollection($categories));
        $this->getValue($product)->shouldReturn('Category1-1/Category1-2/Category1-3|Category2-1/Category2-2');
    }

    private function prepareCategoryCollection(): array
    {
        $categoriesData = [
            [
                'name' => 'Category1-3',
                'id'   => 'id3',
                'path' => 'home|id1|id2|',
            ],
            [
                'name' => 'Category2-2',
                'id'   => 'id5',
                'path' => 'home|id4|',
            ],
            [
                'name' => 'Category1-1',
                'id'   => 'id1',
                'path' => 'home|',
            ],
            [
                'name' => 'Category1-2',
                'id'   => 'id2',
                'path' => 'home|id1|',
            ],
            [
                'name' => 'Category2-1',
                'id'   => 'id4',
                'path' => 'home|',
            ],
        ];

        return array_map(function (array $categoryData) {
            $categoryEntity = new CategoryEntity();
            $categoryEntity->setName($categoryData['name']);
            $categoryEntity->setId($categoryData['id']);
            $categoryEntity->setPath($categoryData['path']);
            return $categoryEntity;
        }, $categoriesData);
    }
}
