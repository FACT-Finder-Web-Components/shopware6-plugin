<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;

class Deeplink implements FieldInterface
{
    public function getName(): string
    {
        return 'Deeplink';
    }

    public function getValue(SalesChannelProductEntity $product): string
    {
        return $product->getSeoUrls()->first()->getSeoPathInfo();
    }
}
