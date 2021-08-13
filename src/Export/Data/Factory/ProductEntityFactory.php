<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Factory;

use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\VariantEntity;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Traversable;

class ProductEntityFactory extends GenericEntityFactory implements FactoryInterface
{
    use FactoryConfigAware;

    private CurrencyFieldsProvider $currencyFieldsProvider;
    private Traversable $variantFields;

    public function __construct(
        PropertyFormatter $propertyFormatter,
        array $fieldProviders,
        CurrencyFieldsProvider $currencyFieldsProvider,
        Traversable $variantFields
    ) {
        parent::__construct($propertyFormatter, $fieldProviders);

        $this->variantFields          = $variantFields;
        $this->currencyFieldsProvider = $currencyFieldsProvider;
    }

    public function handle(Entity $entity): bool
    {
        return in_array(get_class($entity), [Product::class]);
    }

    public function createEntities(Entity $entity): iterable
    {
        $parent = $this->getInstance($entity);
        if ($entity->getChildCount()) {
            yield from $entity->getChildren()->map(fn (
                Product $child) => new VariantEntity($child, $parent->toArray(), $this->propertyFormatter, iterator_to_array($this->variantFields)));
        }
        yield $parent;
    }
}
