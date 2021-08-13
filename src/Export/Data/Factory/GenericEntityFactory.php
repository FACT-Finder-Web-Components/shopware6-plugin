<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Factory;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class GenericEntityFactory implements FactoryInterface
{
    use FactoryConfigAware;

    protected PropertyFormatter $propertyFormatter;
    protected array $fieldProviders;

    public function __construct(PropertyFormatter $propertyFormatter, array $fieldProviders)
    {
        $this->propertyFormatter = $propertyFormatter;
        $this->fieldProviders    = $fieldProviders;
    }

    public function handle(Entity $entity): bool
    {
        return true;
    }

    public function createEntities(Entity $entity): iterable
    {
        yield $this->getInstance($entity);
    }

    protected function getInstance(Entity $entity): ExportEntityInterface
    {
        $type        = get_class($entity);
        $createdType = $this->getCreatedType($type);
        return new $createdType($entity, $this->getFieldProviders($type));
    }

    protected function getFieldProviders(string $type): array
    {
        return $this->fieldProviders[$type] ? iterator_to_array($this->fieldProviders[$type]) : [];
    }
}
