<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\TestUtil;

use ArrayIterator;
use Shopware\Core\Content\Product\ProductEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity as ExportProductEntity;

class ExportProductMockFactory
{
    private SalesChannelProductMockFactory $salesChannelProductMockFactory;

    public function __construct()
    {
        $this->salesChannelProductMockFactory = new SalesChannelProductMockFactory();
    }

    public function create(ProductEntity $product, $data = []): ExportProductEntity
    {
        $productFields = isset($data['productFields']) ? new ArrayIterator($data['productFields']) : new ArrayIterator();
        $cachedProductFields = isset($data['cachedProductFields']) ? new ArrayIterator($data['cachedProductFields']) : new ArrayIterator();
        $entity = new ExportProductEntity($this->salesChannelProductMockFactory->create($product), $productFields, $cachedProductFields);
        $entity->setFilterAttributes($data['filterAttributes'] ?? '');
        $entity->setAdditionalCache(isset($data['additionalCache']) ? new ArrayIterator($data['additionalCache']) : new ArrayIterator());

        return $entity;
    }
}
