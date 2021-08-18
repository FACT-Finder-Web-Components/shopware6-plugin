<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Content\Category\CategoryEntity;

class Deeplink implements FieldInterface
{
    public function getName(): string
    {
        return 'Deeplink';
    }

    public function getValue(Entity $entity): string
    {
        $url = $entity->getSeoUrls()->first();
        return $url ? '/' . ltrim($url->getSeoPathInfo(), '/') : '';
    }

    public function getCompatibleEntityTypes(): array
    {
        return [SalesChannelProductEntity::class, CategoryEntity::class];
    }
}
