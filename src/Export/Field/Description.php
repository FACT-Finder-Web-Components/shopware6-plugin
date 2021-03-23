<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class Description implements FieldInterface
{
    public function getName(): string
    {
        return 'Description';
    }

    public function getValue(Product $product): string
    {
        return (string) $product->getTranslation('description');
    }
}
