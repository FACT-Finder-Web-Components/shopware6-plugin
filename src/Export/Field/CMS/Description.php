<?php


namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;


use Shopware\Core\Content\Category\CategoryEntity as Category;

class Description implements FieldInterface
{
    public function getName(): string
    {
        return 'Description';
    }

    public function getValue(Category $category): string
    {
        return $category->getDescription() ?? '';
    }

}
