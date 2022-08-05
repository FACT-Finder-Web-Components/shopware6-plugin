<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\TestUtil;

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
        $entity = new ExportProductEntity($this->salesChannelProductMockFactory->create($product), [], new \ArrayIterator());
        $entity->setFilterAttributes($data['filterAttributes'] ?? '');

        return $entity;
    }
}
