<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
abstract class AbstractPropertyGroupFilter
{
    public const SELECTED_FILTER_ATTRIBUTES    = 'getDisabledPropertyGroups';
    public const SELECTED_NUMERICAL_ATTRIBUTES = 'getSelectedNumericalAttributes';
    protected string $groupAttribute;

    private PropertyFormatter $propertyFormatter;
    private ExportSettings $exportSettings;

    public function __construct(PropertyFormatter $propertyFormatter, ExportSettings $exportSettings)
    {
        $this->propertyFormatter = $propertyFormatter;
        $this->exportSettings    = $exportSettings;
    }

    public function setGroupAttribute(string $groupAttribute = self::SELECTED_FILTER_ATTRIBUTES): self
    {
        $this->groupAttribute = $groupAttribute;

        return $this;
    }

    public function getGroupAttribute(): string
    {
        return $this->groupAttribute;
    }

    public function getValue(Entity $entity): string
    {
        $attributes = $entity->getChildren()->reduce(
            fn (array $result, Product $child): array => $result + array_map($this->propertyFormatter, $child->getOptions()->getElements()),
            array_map($this->propertyFormatter, $this->applyPropertyGroupsFilter($entity))
        );

        return $attributes ? '|' . implode('|', array_values($attributes)) . '|' : '';
    }

    private function applyPropertyGroupsFilter(Product $product): array
    {
        switch ($this->getGroupAttribute()) {
            case self::SELECTED_FILTER_ATTRIBUTES:
                $disabledPropertyGroups      = call_user_func([$this->exportSettings, self::SELECTED_FILTER_ATTRIBUTES]);
                $selectedNumericalAttributes = call_user_func([$this->exportSettings, self::SELECTED_NUMERICAL_ATTRIBUTES]);

                if (empty($disabledPropertyGroups)) {
                    return [];
                }

                return $product->getProperties()
                    ->filter(fn (PropertyGroupOptionEntity $option): bool => !in_array($option->getGroupId(), $disabledPropertyGroups))
                    ->filter(fn (PropertyGroupOptionEntity $option): bool => !in_array($option->getGroupId(), $selectedNumericalAttributes))
                    ->getElements();

            case self::SELECTED_NUMERICAL_ATTRIBUTES:
                $disabledPropertyGroups      = call_user_func([$this->exportSettings, self::SELECTED_FILTER_ATTRIBUTES]);
                $selectedAttributes = call_user_func([$this->exportSettings, self::SELECTED_NUMERICAL_ATTRIBUTES]);

                return $product->getProperties()
                    ->filter(fn (PropertyGroupOptionEntity $option): bool => !in_array($option->getGroupId(), $disabledPropertyGroups))
                    ->filter(fn (PropertyGroupOptionEntity $option): bool => in_array($option->getGroupId(), $selectedAttributes))
                    ->getElements();

            default:
                return [];
        }
    }
}
