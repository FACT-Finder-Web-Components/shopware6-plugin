<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;

use Shopware\Core\Content\Category\CategoryEntity as Category;

class Master implements FieldInterface
{
    public function getName(): string
    {
        return 'Master';
    }

    public function getValue(Category $category): string
    {
        return $category->getId() ?? '';
    }
}
