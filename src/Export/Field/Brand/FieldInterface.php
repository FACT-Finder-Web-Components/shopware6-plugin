<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field\Brand;

use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity as Brand;

interface FieldInterface
{
    public function getName(): string;

    public function getValue(Brand $brand): string;
}
