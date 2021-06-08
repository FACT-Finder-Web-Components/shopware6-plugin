<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;

class FilterAttributes implements FieldInterface
{
    /** @var PropertyFormatter */
    private $propertyFormatter;

    /** @var ExportSettings */
    private $exportFilters;

    public function __construct(PropertyFormatter $propertyFormatter, ExportSettings $exportFilters)
    {
        $this->propertyFormatter = $propertyFormatter;
        $this->exportFilters     = $exportFilters;
    }

    public function getName(): string
    {
        return 'FilterAttributes';
    }

    public function getValue(Product $product): string
    {
        $attributes = $product->getChildren()->reduce(function (array $result, Product $child): array {
            return $result + array_map($this->propertyFormatter, $child->getOptions()->getElements());
        }, array_map($this->propertyFormatter, $this->applyPropertyGroupsFilter($product)));

        return $attributes ? '|' . implode('|', array_values($attributes)) . '|' : '';
    }

    private function applyPropertyGroupsFilter(Product $product): array
    {
        $disabledProperties = $this->exportFilters->getDisabledPropertyGroups();

        if (!$disabledProperties) {
            return $product->getProperties()->getElements();
        }

        return $product->getProperties()->filter(function (PropertyGroupOptionEntity $option) use ($disabledProperties) {
            return !in_array($option->getGroupId(), $disabledProperties);
        })->getElements();
    }
}
