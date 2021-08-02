<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;

use Shopware\Core\Content\Category\CategoryEntity as Category;

class DeepLink implements FieldInterface
{
    public function getName(): string
    {
        return 'DeepLink';
    }

    public function getValue(Category $category): string
    {
        $url = $category->getSeoUrls()->first();
        return $url ? '/' . ltrim($url->getSeoPathInfo(), '/') : '';
    }
}
