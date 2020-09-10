<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

interface FieldInterface
{
    public function getName(): string;

    public function getValue(Product $product): string;
}
