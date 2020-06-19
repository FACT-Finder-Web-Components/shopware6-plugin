<?php

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;

interface FieldInterface
{
    public function getName(): string;

    public function getValue(SalesChannelProductEntity $product): string;
}
