<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;

use Shopware\Core\Content\Category\CategoryEntity as Category;

class ImageUrl implements FieldInterface
{
    public function getName(): string
    {
        return 'ImageUrl';
    }

    public function getValue(Category $category): string
    {
        return $category->getMedia() ? $category->getMedia()->getUrl() : '';
    }
}
