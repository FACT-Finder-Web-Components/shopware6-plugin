<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;

use Shopware\Core\Content\Category\CategoryEntity as Category;

class PageId implements FieldInterface
{
    public function getName(): string
    {
        return 'PageId';
    }

    public function getValue(Category $category): string
    {
        return $category->getCmsPageId() ?? '';
    }
}
