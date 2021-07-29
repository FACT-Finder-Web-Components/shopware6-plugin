<?php

namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;

use Shopware\Core\Content\Category\CategoryEntity as Category;

interface FieldInterface
{
    public function getName(): string;

    public function getValue(Category $category): string;
}
