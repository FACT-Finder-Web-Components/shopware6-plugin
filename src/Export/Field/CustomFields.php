<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

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

    public function getValue(Product $product): string
    {
        return $this->getFieldValue($product);
    }
}
