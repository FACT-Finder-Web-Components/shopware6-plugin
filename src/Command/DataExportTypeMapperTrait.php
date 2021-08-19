<?php


namespace Omikron\FactFinder\Shopware6\Command;


use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;

trait DataExportTypeMapperTrait
{
    private function getEntityFqnByType(string $exportType): string
    {
        $entityTypeMap = $this->getTypeEntityMap();

        if (isset($entityTypeMap[$exportType])) {
            return $entityTypeMap[$exportType];
        } else {
            throw new \Exception('Unknown export type');
        }
    }

    private function getTypeEntityMap(): array
    {
        return [
            self::PRODUCTS_EXPORT_TYPE => SalesChannelProductEntity::class,
            self::BRANDS_EXPORT_TYPE => ProductManufacturerEntity::class,
            self::CMS_EXPORT_TYPE => CategoryEntity::class
        ];
    }
}
