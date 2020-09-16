<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class Brand implements FieldInterface
{
    public function getName(): string
    {
        return 'Brand';
    }

    public function getValue(Product $product): string
    {
        $manufacturer = $product->getManufacturer();
        return $manufacturer && $manufacturer->getName() ? $manufacturer->getName() : '';
    }
}
