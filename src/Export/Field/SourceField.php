<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\CategoryEntity;

class SourceField implements FieldInterface
{
    private string $categoryPathFieldName;

    public function __construct(string $categoryPathFieldName)
    {
        $this->categoryPathFieldName = $categoryPathFieldName;
    }

    public function getName(): string
    {
        return "sourceField";
    }

    public function getValue(Entity $entity): string
    {
        return $this->categoryPathFieldName;
    }

    public function getCompatibleEntityTypes(): array
    {
        return [CategoryEntity::class];
    }

}
