<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;

use Omikron\FactFinder\Shopware6\Export\Field\AbstractCustomField;
use Shopware\Core\Content\Category\CategoryEntity as Category;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomFields extends AbstractCustomField implements FieldInterface
{
    public function getName(): string
    {
        return 'CustomFields';
    }

    public function getValue(Category $category): string
    {
        return $this->getFieldValue($category);
    }
}
