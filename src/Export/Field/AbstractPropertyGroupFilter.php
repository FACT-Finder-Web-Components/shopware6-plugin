<?php


namespace Omikron\FactFinder\Shopware6\Export\Field;


use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;

abstract class AbstractPropertyGroupFilter
{
    public const SELECTED_FILTER_ATTRIBUTES = 'getSelectedFilterAttributes';
    public const SELECTED_NUMERICAL_ATTRIBUTES = 'getSelectedNumericalAttributes';

    private PropertyFormatter $propertyFormatter;
    private ExportSettings $exportSettings;
    protected string $groupAttribute;

    public function __construct(PropertyFormatter $propertyFormatter, ExportSettings $exportSettings)
    {
        $this->propertyFormatter  = $propertyFormatter;
        $this->exportSettings     = $exportSettings;
    }

    public function setGroupAttribute(string $groupAttribute = self::SELECTED_FILTER_ATTRIBUTES)
    {
        $this->groupAttribute = $groupAttribute;

        return $this;
    }

    public function getValue(Product $product)
    {
        $attributes = $product->getChildren()->reduce(
            fn (array $result, Product $child): array => $result + array_map($this->propertyFormatter, $child->getOptions()->getElements()),
            array_map($this->propertyFormatter, $this->applyPropertyGroupsFilter($product))
        );

        return $attributes ? '|' . implode('|', array_values($attributes)) . '|' : '';
    }

    private function applyPropertyGroupsFilter(Product $product): array
    {
        switch ($this->groupAttribute) {
            case self::SELECTED_FILTER_ATTRIBUTES:
                $selectedAttributes = call_user_func([$this->exportSettings, self::SELECTED_FILTER_ATTRIBUTES]);
                break;
            case self::SELECTED_NUMERICAL_ATTRIBUTES:
                $selectedAttributes = call_user_func([$this->exportSettings, self::SELECTED_NUMERICAL_ATTRIBUTES]);
                break;
            default:
                $selectedAttributes = [];
                break;
        }

        if (empty($selectedAttributes)) {
            return $selectedAttributes;
        }

        return $product->getProperties()
            ->filter(fn (PropertyGroupOptionEntity $option): bool => in_array($option->getGroupId(), $selectedAttributes))
            ->getElements();
    }
}
