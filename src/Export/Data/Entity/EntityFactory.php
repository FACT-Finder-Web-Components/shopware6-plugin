<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\Field\CMS\FieldInterface as CMSFieldInterface;
use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\Brand\FieldInterface as BrandFieldInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity as Brand;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class EntityFactory
{
    private PropertyFormatter $propertyFormatter;

    /** @var FieldInterface[] */
    private array $productFields;

    /** @var CMSFieldInterface[] */
    private array $cmsFields;

    /** @var FieldInterface[] */
    private array $variantFields;

    /** @var BrandFieldInterface[] */
    private array $brandFields;

    private CurrencyFieldsProvider $currencyFieldsProvider;

    public function __construct(
        PropertyFormatter $propertyFormatter,
        iterable $productFields,
        iterable $variantFields,
        CurrencyFieldsProvider $currencyFieldsProvider,
        iterable $brandFields,
        iterable $cmsFields
    ) {
        $this->propertyFormatter      = $propertyFormatter;
        $this->productFields          = iterator_to_array($productFields);
        $this->variantFields          = iterator_to_array($variantFields);
        $this->brandFields            = iterator_to_array($brandFields);
        $this->currencyFieldsProvider = $currencyFieldsProvider;
        $this->cmsFields              = iterator_to_array($cmsFields);
    }

    /**
     * @param Product|Brand|Category $data
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
                $entity = new BrandEntity($data, $this->brandFields);

                break;
            case $data instanceof Category:
                $entity = new CategoryEntity($data, $this->cmsFields);

                break;
            default:
                throw new \Exception('Unknown entity ' . get_class($data));
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
