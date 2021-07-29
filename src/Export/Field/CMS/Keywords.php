<?php


namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;


use Shopware\Core\Content\Category\CategoryEntity as Category;

class Keywords implements FieldInterface
{
    public function getName(): string
    {
        return 'Keywords';
    }

    public function getValue(Category $category): string
    {
        return $category->getKeywords() ?? '';
    }

}
