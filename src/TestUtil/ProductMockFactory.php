<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\TestUtil;

use Shopware\Core\Content\Product\DataAbstractionLayer\VariantListingConfig;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ProductMockFactory
{
    public function create(array $data = []): ProductEntity
    {
        $productEntity = new ProductEntity();
        $productEntity->setId($data['id'] ?? Uuid::randomHex());
        $productEntity->setProductNumber($data['productNumber'] ?? 'SW100');
        $productEntity->setVariantListingConfig(
            new VariantListingConfig(
                true,
                $productEntity->getId(),
                $data['configuratorGroupConfig'] ?? self::getGroupConfigurationConfig(
                    [
                        [md5('size'), 'false'],
                        [md5('color'), 'true'],
                        [md5('material'), 'true'],
                    ]
                )
            ));

        return $productEntity;
    }

    /**
     * $groupConfig in format
     *  [[groupId, true|false]].
     *
     * @param array $groupsConfig
     *
     * @return array
     */
    public static function getGroupConfigurationConfig(array $groupsConfig): array
    {
        $row        = '{"id": "%s", "representation": "box", "expressionForListings": %s}';
        $jsonConfig = array_map(fn (array $groupConfig): array => json_decode(sprintf($row, ...$groupConfig), true), $groupsConfig);

        return $jsonConfig;
    }
}
