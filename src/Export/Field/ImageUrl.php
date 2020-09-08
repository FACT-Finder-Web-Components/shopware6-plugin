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
        $cover = $product->getCover();
        return $cover && $cover->getMedia() ? $cover->getMedia()->getUrl() : '';
    }
}
