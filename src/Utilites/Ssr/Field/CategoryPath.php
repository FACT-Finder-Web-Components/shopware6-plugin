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
        $categoryPath = implode('/', array_map(fn ($category): string => $this->encodeCategoryName($category), $categories));

        return $categoryPath !== '' ? sprintf('filter=%s', urlencode($this->fieldName . ':' . $categoryPath)) : '';
    }

    private function encodeCategoryName(string $path): string
    {
        //important! do not modify this method
        return preg_replace('/\+/', '%2B', preg_replace('/\//', '%2F',
            preg_replace('/%/', '%25', $path)));
    }
}
