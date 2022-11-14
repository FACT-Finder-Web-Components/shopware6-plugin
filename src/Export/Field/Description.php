<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class Description implements FieldInterface
{
    public function getName(): string
    {
        return 'Description';
    }

    public function getValue(Entity $entity): string
    {
        return (string) $entity->getTranslation('description');
    }

    public function getCompatibleEntityTypes(): array
    {
        return [ProductEntity::class];
    }
}
