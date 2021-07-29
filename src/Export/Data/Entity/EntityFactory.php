<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\Field\CMS\FieldInterface as CMSFieldInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Category\CategoryEntity as Category;
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

    private CurrencyFieldsProvider $currencyFieldsProvider;

    public function __construct(
        PropertyFormatter $propertyFormatter,
        iterable $productFields,
        iterable $variantFields,
        CurrencyFieldsProvider $currencyFieldsProvider,
        iterable $cmsFields
    ) {
        $this->propertyFormatter      = $propertyFormatter;
        $this->productFields          = iterator_to_array($productFields);
        $this->variantFields          = iterator_to_array($variantFields);
        $this->currencyFieldsProvider = $currencyFieldsProvider;
        $this->cmsFields              = iterator_to_array($cmsFields);
    }

    /**
     * @param Product | Category $data
     *
     * @return iterable
     */
    public function createEntities($data): iterable
    {
        $entity = $data instanceof Category
            ? new CategoryEntity($data, $this->cmsFields)
            : new ProductEntity($data, array_merge($this->productFields, $this->currencyFieldsProvider->getCurrencyFields()));

        if ($data->getChildCount()) {
            $parentData = $entity->toArray();
            $data instanceof Product ?? yield from $data->getChildren()->map(fn (Product $child) => new VariantEntity($child, $parentData, $this->propertyFormatter, $this->variantFields));
        }
        yield $entity;
    }
}
