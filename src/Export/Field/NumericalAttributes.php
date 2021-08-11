<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class NumericalAttributes extends AbstractPropertyGroupFilter implements FieldInterface
{
    public function getName(): string
    {
        return 'NumericalAttributes';
    }

    public function getValue(Product $product): string
    {
        parent::setGroupAttribute(parent::SELECTED_NUMERICAL_ATTRIBUTES);
        return parent::getValue($product);
    }
}
