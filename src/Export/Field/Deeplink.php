<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class Deeplink implements FieldInterface
{
    public function getName(): string
    {
        return 'Deeplink';
    }

    public function getValue(Product $product): string
    {
        $url = $product->getSeoUrls()->first();
        return $url ? '/' . ltrim($url->getSeoPathInfo(), '/') : '';
    }
}
