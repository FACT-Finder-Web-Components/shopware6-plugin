<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class ImageUrl implements FieldInterface
{
    public function getName(): string
    {
        return 'ImageUrl';
    }

    public function getValue(Product $product): string
    {
        return $product->getCover()->getMedia()->getUrl();
    }
}
