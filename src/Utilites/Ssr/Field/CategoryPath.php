<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Field;

use Shopware\Core\Content\Category\CategoryEntity;

class CategoryPath
{
    private string $fieldName;

    public function __construct(string $categoryPathFieldName)
    {
        $this->fieldName = $categoryPathFieldName;
    }

    public function getValue(CategoryEntity $categoryEntity): string
    {
        $categories   = array_slice($categoryEntity->getBreadcrumb(), 1);

        return implode(',', $categories);
    }
}
