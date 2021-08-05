<?php


namespace Omikron\FactFinder\Shopware6\Export\Field\Manufacturer;


use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity as Brand;

interface FieldInterface
{
    public function getName(): string;
    public function getValue(Brand $brand): string;
}
