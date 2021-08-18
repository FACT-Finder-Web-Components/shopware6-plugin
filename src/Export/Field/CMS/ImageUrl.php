<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class ImageUrl implements FieldInterface
{
    public function getName(): string
    {
        return 'ImageUrl';
    }

    public function getValue(Entity $entity): string
    {
        return $entity->getMedia() ? $entity->getMedia()->getUrl() : '';
    }

    public function getCompatibleEntityTypes(): array
    {
        return [CategoryEntity::class];
    }
}
