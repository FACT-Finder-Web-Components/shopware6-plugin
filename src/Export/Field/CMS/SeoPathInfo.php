<?php


namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;


use Shopware\Core\Content\Category\CategoryEntity as Category;

class SeoPathInfo implements FieldInterface
{
    public function getName(): string
    {
        return 'SeoPathInfo';
    }

    public function getValue(Category $category): string
    {
        return $category->getSeoUrls()->first()->getSeoPathInfo() ?? '';
    }

}
