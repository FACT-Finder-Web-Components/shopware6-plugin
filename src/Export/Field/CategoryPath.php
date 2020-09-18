<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Category\CategoryEntity as Category;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class CategoryPath implements FieldInterface
{
    /** @var string */
    private $fieldName;

    public function __construct(string $fieldName = 'CategoryPath')
    {
        $this->fieldName = $fieldName;
    }

    public function getName(): string
    {
        return $this->fieldName;
    }

    public function getValue(Product $product): string
    {
        $categoryName = $this->categoryName($product);
        return implode('|', $product->getCategories()->fmap(function (Category $category) use ($categoryName): string {
            $path = explode('|', trim($category->getPath() . $category->getId(), '|'));
            return implode('/', array_map($categoryName, array_slice($path, 1)));
        }));
    }

    private function categoryName(Product $product): callable
    {
        $names = $product->getCategoriesRo()->map(function (Category $category): string {
            return (string) $category->getName();
        });

        return function (string $id) use ($names): string {
            return rawurlencode($names[$id] ?? '');
        };
    }
}
