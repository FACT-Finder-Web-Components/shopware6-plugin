<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class CustomFields implements FieldInterface
{
    /** @var PropertyFormatter */
    private $propertyFormatter;

    public function __construct(PropertyFormatter $propertyFormatter)
    {
        $this->propertyFormatter = $propertyFormatter;
    }

    public function getName(): string
    {
        return 'CustomFields';
    }

    public function getValue(Product $product): string
    {
        $fields = $product->getCustomFields() ?? [];
        $value  = array_map([$this->propertyFormatter, 'format'], array_keys($fields), array_values($fields));
        return $value ? '|' . implode('|', $value) . '|' : '';
    }
}
