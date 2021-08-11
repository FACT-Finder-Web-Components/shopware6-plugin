<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class FilterAttributes implements FieldInterface
{
    private PropertyFormatter $propertyFormatter;
    private ExportSettings $exportSettings;

    public function __construct(PropertyFormatter $propertyFormatter, ExportSettings $exportSettings)
    {
        $this->propertyFormatter  = $propertyFormatter;
        $this->exportSettings     = $exportSettings;
    }

    public function getName(): string
    {
        return 'FilterAttributes';
    }

    /**
     * @param Product $entity
     *
     * @return string
     */
    public function getValue(Entity $entity): string
    {
        $attributes = $entity->getChildren()->reduce(
            fn (array $result, Product $child): array => $result + array_map($this->propertyFormatter, $child->getOptions()->getElements()),
            array_map($this->propertyFormatter, $this->applyPropertyGroupsFilter($entity))
        );

        return $attributes ? '|' . implode('|', array_values($attributes)) . '|' : '';
    }

    public function getCompatibleEntityTypes(): array
    {
        return [Product::class];
    }

    private function applyPropertyGroupsFilter(Product $product): array
    {
        $selectedFilterAttributes = $this->exportSettings->getSelectedFilterAttributes();

        if (empty($selectedFilterAttributes)) {
            return [];
        }

        return $product->getProperties()
                       ->filter(fn (PropertyGroupOptionEntity $option): bool => in_array($option->getGroupId(), $selectedFilterAttributes))
                       ->getElements();
    }
}
