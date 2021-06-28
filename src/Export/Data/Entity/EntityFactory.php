<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class EntityFactory
{
    private PropertyFormatter $propertyFormatter;

    /** @var FieldInterface[] */
    private array $productFields;

    /** @var FieldInterface[] */
    private array $variantFields;

    private CurrencyFieldsProvider $currencyFieldsProvider;

    public function __construct(
        PropertyFormatter $propertyFormatter,
        iterable $productFields,
        iterable $variantFields,
        CurrencyFieldsProvider $currencyFieldsProvider
    ) {
        $this->propertyFormatter      = $propertyFormatter;
        $this->productFields          = iterator_to_array($productFields);
        $this->variantFields          = iterator_to_array($variantFields);
        $this->currencyFieldsProvider = $currencyFieldsProvider;
    }

    /**
     * @param Product $product
     *
     * @return ExportEntityInterface[]
     */
    public function createEntities(Product $product): iterable
    {
        $entity = new ProductEntity($product, array_merge($this->productFields, $this->currencyFieldsProvider->getCurrencyFields()));
        if ($product->getChildCount()) {
            $parentData = $entity->toArray();
            yield from $product->getChildren()->map(fn (Product $child) => new VariantEntity($child, $parentData, $this->propertyFormatter, $this->variantFields));
        }
        yield $entity;
    }
}
