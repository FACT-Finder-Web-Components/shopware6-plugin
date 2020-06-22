<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\SalesChannelProvider;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;

class ImageUrl implements FieldInterface
{
    private $salesChannelProvider;

    public function __construct(SalesChannelProvider $salesChannelProvider)
    {
        $this->salesChannelProvider = $salesChannelProvider;
    }

    public function getName(): string
    {
        return 'ImageUrl';
    }

    public function getValue(SalesChannelProductEntity $product): string
    {
        $domain = $this->salesChannelProvider->getSalesChannelContext()->getSalesChannel()->getDomains()->first()->getUrl();
        return $domain . '/' . $product->getCover()->getMedia()->getUrl();
    }
}
