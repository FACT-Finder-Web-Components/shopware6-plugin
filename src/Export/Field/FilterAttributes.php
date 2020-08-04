<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class FilterAttributes implements FieldInterface
{
    /** @var PropertyFormatter */
    private $propertyFormatter;

    public function __construct(PropertyFormatter $propertyFormatter)
    {
        $this->propertyFormatter = $propertyFormatter;
    }

    public function getName(): string
    {
        return 'FilterAttributes';
    }

    public function getValue(Product $product): string
    {
        $attributes = $product->getChildren()->reduce(function (array $result, Product $child): array {
            return $result + array_map($this->propertyFormatter, $child->getOptions()->getElements());
        }, array_map($this->propertyFormatter, $product->getProperties()->getElements()));

        return $attributes ? '|' . implode('|', array_values($attributes)) . '|' : '';
    }
}
