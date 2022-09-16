<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\TestUtil;

use ArrayIterator;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity as ExportProductEntity;
use Shopware\Core\Content\Product\ProductEntity;

class ExportProductMockFactory
{
    private SalesChannelProductMockFactory $productMockFactory;

    public function __construct()
    {
        $this->productMockFactory = new SalesChannelProductMockFactory();
    }

    public function create(ProductEntity $product, $data = []): ExportProductEntity
    {
        $productFields       = isset($data['productFields']) ? new ArrayIterator($data['productFields']) : new ArrayIterator();
        $cachedProductFields = isset($data['cachedProductFields']) ? new ArrayIterator($data['cachedProductFields']) : new ArrayIterator();
        $entity              = new ExportProductEntity($this->productMockFactory->create($product), $productFields, $cachedProductFields);
        $entity->setFilterAttributes($data['filterAttributes'] ?? '');
        $entity->setAdditionalCache(isset($data['additionalCache']) ? new ArrayIterator($data['additionalCache']) : new ArrayIterator());

        return $entity;
    }
}
