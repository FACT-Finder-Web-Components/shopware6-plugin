<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
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
        return $this->checkAndReturnMediaUrlOrEmptyString($entity);
    }

    public function getCompatibleEntityTypes(): array
    {
        return [SalesChannelProductEntity::class, CategoryEntity::class, ProductManufacturerEntity::class];
    }

    private function checkAndReturnMediaUrlOrEmptyString(?Entity $entity = null): string
    {
        $media = $entity->getMedia();

        if (!$media) return '';

        if (method_exists($media, 'first')) {
            if (!is_null($media->first())) {
                return $media->first()->getMedia()->getUrl();
            } else {
                return '';
            }
        }

        return $media->getUrl();
    }
}
