<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Filter\TextFilter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;

class Attributes implements FieldInterface
{
    /** @var TextFilter */
    private $filter;

    public function __construct(TextFilter $filter)
    {
        $this->filter = $filter;
    }

    public function getName(): string
    {
        return 'Attributes';
    }

    public function getValue(SalesChannelProductEntity $product): string
    {
        $attributes = $product->getProperties()->reduce(function (array $attrs, PropertyGroupOptionEntity $property) {
            return $attrs + [$property->getId() => $this->formatAttribute($property->getGroup()->getName(), $property->getName())];
        }, []);

        return $attributes ? '|' . implode('|', array_values($attributes)) . '|' : '';
    }

    private function formatAttribute($name, $value): string
    {
        return "{$this->filter->filterValue($name)}={$this->filter->filterValue($value)}";
    }
}
