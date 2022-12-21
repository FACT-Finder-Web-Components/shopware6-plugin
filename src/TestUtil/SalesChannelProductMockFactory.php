<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\TestUtil;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;

class SalesChannelProductMockFactory
{
    public function create(ProductEntity $product): SalesChannelProductEntity
    {
        $entity = new SalesChannelProductEntity();
        $entity->setId($product->getId());
        $entity->setProductNumber($product->getProductNumber());
        $entity->setParentId($product->getParentId());

        if ($product->getParent() !== null) {
            $entity->setOptions($product->getOptions());
        }

        if ($product->getParent() !== null) {
            $entity->setParent($product->getParent());
        }

        return $entity;
    }
}
