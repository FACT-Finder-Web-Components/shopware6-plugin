<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity as Brand;

class EntityFactory
{
    private PropertyFormatter $propertyFormatter;

    /** @var FieldInterface[] */
    private array $productFields;

    /** @var \Omikron\FactFinder\Shopware6\Export\Field\Manufacturer\FieldInterface[] */
    private array $manufacturerFields;

    /** @var FieldInterface[] */
    private array $variantFields;

    private CurrencyFieldsProvider $currencyFieldsProvider;

    public function __construct(
        PropertyFormatter $propertyFormatter,
        iterable $productFields,
        iterable $variantFields,
        CurrencyFieldsProvider $currencyFieldsProvider,
        iterable $manufacturerFields
    ) {
        $this->propertyFormatter      = $propertyFormatter;
        $this->productFields          = iterator_to_array($productFields);
        $this->variantFields          = iterator_to_array($variantFields);
        $this->manufacturerFields     = iterator_to_array($manufacturerFields);
        $this->currencyFieldsProvider = $currencyFieldsProvider;
    }

    /**
     * @param Product | Brand $data
     *
     * @return ExportEntityInterface[]
     */
    public function createEntities($data): iterable
    {
        switch (true) {
            case $data instanceof Product:
                $entity = new ProductEntity($data, array_merge($this->productFields, $this->currencyFieldsProvider->getCurrencyFields()));
                break;
            case $data instanceof Brand:
                $entity = new BrandEntity($data);
                break;
        }

        if (method_exists($data, 'getChildCount')) {
            if ($data->getChildCount()) {
                $parentData = $entity->toArray();

                if ($data instanceof Product) {
                    yield from $data->getChildren()->map(fn (Product $child) => new VariantEntity($child, $parentData, $this->propertyFormatter, $this->variantFields));
                }
            }
        }

        yield $entity;
    }
}
