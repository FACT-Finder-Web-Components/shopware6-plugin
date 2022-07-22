<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\TestUtil;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Content\Product\ProductEntity;

class SalesChannelProductMockFactory
{
    public function create(ProductEntity $product): SalesChannelProductEntity
    {
        $entity = new SalesChannelProductEntity();
        $entity->setId($product->getId());
        $entity->setProductNumber($product->getProductNumber());
        $entity->setParent($product->getParent());
        $entity->setParentId($product->getParentId());
        $entity->setOptions($product->getOptions());

        return $entity;
    }
}
