<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field\Brand;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class BrandLogo implements FieldInterface
{
    public function getName(): string
    {
        return 'BrandLogo';
    }

    public function getValue(Entity $entity): string
    {
        return $entity->getMedia() ? $entity->getMedia()->getUrl() : '';
    }

    public function getCompatibleEntityTypes(): array
    {
        return [];
    }
}
