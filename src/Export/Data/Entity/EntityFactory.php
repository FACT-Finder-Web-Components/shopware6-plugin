<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class EntityFactory
{
    /** @var PropertyFormatter */
    private $propertyFormatter;

    /** @var FieldInterface[] */
    private $productFields;

    /** @var FieldInterface[] */
    private $variantFields;

    public function __construct(PropertyFormatter $propertyFormatter, iterable $productFields, iterable $variantFields)
    {
        $this->propertyFormatter = $propertyFormatter;
        $this->productFields     = iterator_to_array($productFields);
        $this->variantFields     = iterator_to_array($variantFields);
    }

    /**
     * @param Product $product
     *
     * @return ExportEntityInterface[]
     */
    public function createEntities(Product $product): iterable
    {
        $entity = new ProductEntity($product, $this->productFields);
        if ($product->getChildCount()) {
            $parentData = $entity->toArray();
            yield from $product->getChildren()->map(function (Product $child) use ($parentData) {
                return new VariantEntity($child, $parentData, $this->propertyFormatter, $this->variantFields);
            });
        }
        yield $entity;
    }
}
