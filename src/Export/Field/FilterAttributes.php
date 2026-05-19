<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity;
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
     */
    public function getValue(Entity $entity): string
    {
        // 1. Bazowe właściwości (dziedziczone lub bezpośrednie)
        $properties = $this->applyPropertyGroupsFilter($entity);
        $attributes = $properties ? array_map($this->propertyFormatter, $properties) : [];

        // 2. Pobieranie opcji bez ładowania całych encji dzieci
        if ($entity->getParentId() !== null) {
            // Jesteśmy w wariancie - pobieramy jego konkretne opcje
            $options = $entity->getOptions() ? $entity->getOptions()->getElements() : [];
            $attributes = array_merge($attributes, array_map($this->propertyFormatter, $options));
        } else {
            // Jesteśmy w produkcie głównym - pobieramy agregację opcji ze wszystkich wariantów
            $configuratorSettings = $entity->getConfiguratorSettings();
            if ($configuratorSettings) {
                $options = [];
                foreach ($configuratorSettings as $setting) {
                    if ($setting->getOption()) {
                        $options[] = $setting->getOption();
                    }
                }
                $attributes = array_merge($attributes, array_map($this->propertyFormatter, $options));
            }
        }

        return $attributes ? '|' . implode('|', array_unique(array_values($attributes))) . '|' : '';
    }

    public function getCompatibleEntityTypes(): array
    {
        return [ProductEntity::class];
    }

    private function applyPropertyGroupsFilter(Product $product): array
    {
        $disabledProperties = $this->exportSettings->getDisabledPropertyGroups();

        if (!$disabledProperties) {
            return $product->getProperties() ? $product->getProperties()->getElements() : [];
        }

        return $product->getProperties()
            ->filter(fn (PropertyGroupOptionEntity $option): bool => !in_array($option->getGroupId(), $disabledProperties))
            ->getElements();
    }
}
