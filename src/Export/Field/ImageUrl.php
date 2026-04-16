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

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function checkAndReturnMediaUrlOrEmptyString(?Entity $entity = null): string
    {
        if ($entity === null) {
            return '';
        }

        if (method_exists($entity, 'getCover')) {
            $cover = $entity->getCover();

            if ($cover && $cover->getMedia() && !empty($cover->getMedia()->getUrl())) {
                return $cover->getMedia()->getUrl();
            }
        }

        if (method_exists($entity, 'getMedia')) {
            $media = $entity->getMedia();

            if ($media) {
                if (method_exists($media, 'first')) {
                    $firstElement = $media->first();

                    return $firstElement ? $this->checkAndReturnMediaUrlOrEmptyString($firstElement) : '';
                }

                if (method_exists($media, 'getUrl')) {
                    return $media->getUrl() ?? '';
                }
            }
        }

        return '';
    }
}
