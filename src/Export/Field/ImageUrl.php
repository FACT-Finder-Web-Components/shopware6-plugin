<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Data\Entity\BrandEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\CmsPageEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity;
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
        return [ProductEntity::class, CmsPageEntity::class, BrandEntity::class];
    }

    private function checkAndReturnMediaUrlOrEmptyString(?Entity $entity = null): string
    {
        $media = $entity ? $entity->getMedia() : null;

        if ($media) {
            if (method_exists($media, 'first')) {
                return $this->checkAndReturnMediaUrlOrEmptyString($media->first());
            }
            return $media->getUrl();
        }

        return '';
    }
}
