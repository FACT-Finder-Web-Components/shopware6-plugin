<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Config\ExcludedFields;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;

class FilterAttributes implements FieldInterface
{
    /** @var PropertyFormatter */
    private $propertyFormatter;

    private ExcludedFields $excludedFields;

    public function __construct(PropertyFormatter $propertyFormatter, ExcludedFields $excludedFields)
    {
        $this->propertyFormatter = $propertyFormatter;
        $this->excludedFields = $excludedFields;
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
        $disabledPropertyGroups =  $this->excludedFields->getDisabledPropertyGroups();
        $productProperties = $product->getProperties()->getElements();

        if ($disabledPropertyGroups) {
            for ($i = 0; $i<count($disabledPropertyGroups); $i++) {
                /**
                 * @var string $key
                 * @var PropertyGroupOptionEntity $value
                 */
                foreach ($productProperties as $key => $value) {
                    if ($disabledPropertyGroups[$i] == $value->getGroupId()) {
                        unset($productProperties[$key]);
                    }
                }
            }
        }

        return $productProperties;
    }
}
