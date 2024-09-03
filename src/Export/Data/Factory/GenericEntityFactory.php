<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Factory;

use Omikron\FactFinder\Shopware6\Export\FieldsProvider;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class GenericEntityFactory implements FactoryInterface
{
    protected PropertyFormatter $propertyFormatter;
    protected FieldsProvider $fieldsProvider;
    protected array $exportedEntityTypes;

    public function __construct(
        PropertyFormatter $propertyFormatter,
        FieldsProvider $fieldsProviders,
        \Traversable $exportedEntityTypes,
    ) {
        $this->propertyFormatter   = $propertyFormatter;
        $this->fieldsProvider      = $fieldsProviders;
        $this->exportedEntityTypes = iterator_to_array($exportedEntityTypes);
    }

    public function handle(Entity $entity): bool
    {
        return !$entity instanceof SalesChannelProductEntity;
    }

    public function createEntities(Entity $entity, string $producedType): iterable
    {
        yield new $producedType($entity, $this->fieldsProvider->getFields($producedType));
    }
}
