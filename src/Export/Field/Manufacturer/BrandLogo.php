<?php


namespace Omikron\FactFinder\Shopware6\Export\Field\Manufacturer;

use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity as Brand;

class BrandLogo implements FieldInterface
{
    public function getName(): string
    {
        return 'BrandLogo';
    }

    public function getValue(Brand $brand): string
    {
        return 'brand_logo';
    }
}
