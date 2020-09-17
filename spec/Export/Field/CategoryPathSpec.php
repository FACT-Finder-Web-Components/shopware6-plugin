<?php

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\CategoryPath;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Category;
use PhpSpec\ObjectBehavior;

class CategoryPathSpec extends ObjectBehavior
{
    private $categoryCollection;

    function let(SalesChannelRepositoryInterface $salesChannelRepository, SalesChannelService $salesChannelService)
    {
        $this->beConstructedWith($salesChannelRepository, $salesChannelService, 'CategoryPath');
        $this->categoryCollection = new CategoryCollection($this->prepareCategoryCollection());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CategoryPath::class);
    }

    function it_does_not_fail_if_product_is_not_assigned_to_any_category(Product $product)
    {
        $this->shouldNotThrow()->during('getValue', [$product]);
        $product->getCategories()->willReturn(null);
        $product->getCategoriesRo()->willReturn(null);
        $this->getValue($product)->shouldReturn('');
    }

    function it_does_not_fail_if_any_of_category_has_no_path(Product $product)
    {
        $product->getCategories()->willReturn($this->categoryCollection);
        $product->getCategoriesRo()->willReturn($this->categoryCollection);
        $this->getValue($product)->shouldReturn('Category1|Category2|Category3|Category2-1|Category2-2');
    }

    private function prepareCategoryCollection(): array
    {
        $collection = [];
        foreach ([
                     [
                         'name' => 'Category1',
                         'id'   => 'id1',
                         'path' => '|home|'
                     ],
                     [
                         'name' => 'Category2',
                         'id'   => 'id2',
                         'path' => '|home/id1|'
                     ],
                     [
                         'name' => 'Category3',
                         'id'   => 'id3',
                         'path' => '|home/id1/id2|'
                     ],
                     [
                         'name' => 'Category2-1',
                         'id'   => 'id4',
                         'path' => '|home|'
                     ],
                     [
                         'name' => 'Category2-2',
                         'id'   => 'id5',
                         'path' => '|home/id4|'
                     ]
                 ] as $categoryEntry) {
            $categoryEntity = new CategoryEntity();
            $categoryEntity->setName($categoryEntry['name']);
            $categoryEntity->setId($categoryEntry['id']);
            $categoryEntity->setPath($categoryEntry['path']);
            $collection[] = $categoryEntity;
        }

        return $collection;
    }
}
