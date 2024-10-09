<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Factory;

use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\VariantEntity;
use Omikron\FactFinder\Shopware6\Export\FieldsProvider;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class ProductEntityFactory implements FactoryInterface
{
    protected PropertyFormatter $propertyFormatter;
    protected FieldsProvider $fieldsProvider;
    private CurrencyFieldsProvider $currencyFieldsProvider;
    private \Traversable $variantFields;

    public function __construct(
        PropertyFormatter $propertyFormatter,
        FieldsProvider $fieldsProviders,
        CurrencyFieldsProvider $currencyFieldsProvider,
        \Traversable $variantFields,
    ) {
        $this->propertyFormatter      = $propertyFormatter;
        $this->fieldsProvider         = $fieldsProviders;
        $this->variantFields          = $variantFields;
        $this->currencyFieldsProvider = $currencyFieldsProvider;
    }

    public function handle(Entity $entity): bool
    {
        return in_array(get_class($entity), [SalesChannelProductEntity::class]);
    }

    /**
     * @param Entity $entity
     * @param string $producedType
     *
     * @return ProductEntity[]|iterable
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createEntities(Entity $entity, string $producedType = ProductEntity::class): iterable
    {
        // @todo use spread operator?
        $fields = array_merge($this->fieldsProvider->getFields($producedType), $this->currencyFieldsProvider->getCurrencyFields());
        $parent = new $producedType($entity, new \ArrayIterator($fields), new \ArrayIterator());
        if ($entity->getChildCount()) {
            yield from $entity->getChildren()->map(fn (
                SalesChannelProductEntity $child) => new VariantEntity($child, $parent->toArray(), $this->propertyFormatter, iterator_to_array($this->variantFields)));
        }
        yield $parent;
    }
}
