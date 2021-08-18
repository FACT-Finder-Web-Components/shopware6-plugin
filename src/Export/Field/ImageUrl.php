<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class ImageUrl implements FieldInterface
{
    public function getName(): string
    {
        return 'ImageUrl';
    }

    public function getValue(Entity $entity): string
    {
        $cover = $entity->getCover();
        return $cover && $cover->getMedia() ? $cover->getMedia()->getUrl() : '';
    }

    public function getCompatibleEntityTypes(): array
    {
        return [SalesChannelProductEntity::class];
    }
}
