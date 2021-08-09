<?php


namespace Omikron\FactFinder\Shopware6\Export\Field;


use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class NumericalAttributes implements FieldInterface
{
    public function getName(): string
    {
        return 'NumericalAttributes';
    }

    public function getValue(Product $product): string
    {
        return 'num_attr';
    }

}
