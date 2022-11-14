<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class Brand implements FieldInterface
{
    public function getName(): string
    {
        return 'Brand';
    }

    public function getValue(Entity $entity): string
    {
        return $entity->getManufacturer() ? (string) $entity->getManufacturer()->getTranslation('name') : '';
    }

    public function getCompatibleEntityTypes(): array
    {
        return [ProductEntity::class];
    }
}
