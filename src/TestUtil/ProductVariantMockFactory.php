<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\TestUtil;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionCollection;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOptionTranslation\PropertyGroupOptionTranslationCollection;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOptionTranslation\PropertyGroupOptionTranslationEntity;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupTranslation\PropertyGroupTranslationCollection;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupTranslation\PropertyGroupTranslationEntity;
use Shopware\Core\Content\Property\PropertyGroupEntity;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ProductVariantMockFactory
{
    public function create(
        ProductEntity $parent,
        array $data = []
    ): ProductEntity {
        $variant  = new ProductEntity();
        $size     = $data['size'] ?? 'S';
        $color    = $data['color'] ?? 'red';
        $material = $data['material'] ?? 'cotton';
        $variant->setParent($parent);
        $variant->setProductNumber($data['productNumber'] ?? 'SW100.1');
        $variant->setId($data['id'] ?? Uuid::randomHex());
        $variant->setParentId($parent->getId());
        $variant->setOptions($data['options'] ?? self::createPropertyGroupOptionCollection(
            [
                [hash('sha512', 'size'), hash('sha512', $size), 'size', $size],
                [hash('sha512', 'color'), hash('sha512', $color), 'color', $color],
                [hash('sha512', 'material'), hash('sha512', $material), 'material', $material],
            ]
        ));

        return $variant;
    }

    /**
     * $optionsConfig in format
     *  [['groupId','optionId', 'groupName', 'optionName']].
     *
     * @param array $optionsConfig
     *
     * @return PropertyGroupOptionCollection
     */
    public static function createPropertyGroupOptionCollection(array $optionsConfig): PropertyGroupOptionCollection
    {
        $options    = array_reduce($optionsConfig, function (array $carriedOptions, array $optionConfig): array {
            list($groupId, $optionId, $groupName, $optionName) = $optionConfig;
            $group                                             = new PropertyGroupEntity();
            $group->setId($groupId);

            $groupTranslation = new PropertyGroupTranslationEntity();
            $groupTranslation->setPropertyGroupId(hash('sha512', $groupId));
            $groupTranslation->setName($groupName);
            $groupTranslation->setUniqueIdentifier(Uuid::randomHex());

            $group->setTranslations(new PropertyGroupTranslationCollection([hash('sha512', $groupName) => $groupTranslation]));
            $group->addTranslated('name', $groupName);

            $option = new PropertyGroupOptionEntity();
            $option->setId($optionId);

            $optionTranslation = new PropertyGroupOptionTranslationEntity();
            $optionTranslation->setPropertyGroupOptionId(hash('sha512', $optionName));
            $optionTranslation->setName($optionName);
            $optionTranslation->setUniqueIdentifier(Uuid::randomHex());

            $option->setTranslations(new PropertyGroupOptionTranslationCollection([hash('sha512', $optionName) => $optionTranslation]));
            $option->addTranslated('name', $optionName);
            $option->setGroupId($groupId);
            $option->setGroup($group);

            return array_merge($carriedOptions, [$optionId => $option]);
        }, []);

        return new PropertyGroupOptionCollection($options);
    }
}
