<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Formatter\NumberFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class Price implements FieldInterface
{
    private NumberFormatter $numberFormatter;

    public function __construct(NumberFormatter $numberFormatter)
    {
        $this->numberFormatter = $numberFormatter;
    }

    public function getName(): string
    {
        return 'Price';
    }

    public function getValue(Entity $entity): string
    {
        return $this->numberFormatter->format((float)$entity->getCalculatedPrice()->getTotalPrice());
    }

    public function getCompatibleEntityTypes(): array
    {
        return [Product::class];
    }
}
