<?php


namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;


use Shopware\Core\Content\Category\CategoryEntity as Category;

class Name implements FieldInterface
{
    public function getName(): string
    {
        return 'Name';
    }

    public function getValue(Category $category): string
    {
        return $category->getName() ?? '';
    }

}
