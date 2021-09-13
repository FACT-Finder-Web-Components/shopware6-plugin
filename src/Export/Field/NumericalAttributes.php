<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class NumericalAttributes extends AbstractPropertyGroupFilter implements FieldInterface
{
    public function getValue(Entity $entity): string
    {
        parent::setGroupAttribute(self::SELECTED_NUMERICAL_ATTRIBUTES);
        return parent::getValue($entity);
    }

    public function getName(): string
    {
        return 'NumericalAttributes';
    }

    public function getCompatibleEntityTypes(): array
    {
        return [Product::class];
    }
}
