<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Traversable;

class FieldsProvider
{
    private array $exportedFields;
    private array $cache;

    public function __construct(Traversable $exportedFields)
    {
        $this->exportedFields = iterator_to_array($exportedFields);
    }

    public function getFields(string $entityClass): array
    {
        if (!isset($this->cache[$entityClass])) {
            $this->cache[$entityClass] = array_filter($this->exportedFields, fn(
                FieldInterface $field): bool => in_array($entityClass, $field->getCompatibleEntityTypes()));
        }
        return $this->cache[$entityClass];
    }
}
