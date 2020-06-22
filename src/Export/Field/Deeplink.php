<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\SalesChannelProvider;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class Deeplink implements FieldInterface
{
    /** @var SalesChannelProvider */
    private $salesChannelProvider;

    public function __construct(SalesChannelProvider $salesChannelProvider)
    {
        $this->salesChannelProvider = $salesChannelProvider;
    }

    public function getName(): string
    {
        return 'Deeplink';
    }

    public function getValue(Product $product): string
    {
        $domain = $this->salesChannelProvider->getSalesChannelContext()->getSalesChannel()->getDomains()->first()->getUrl();
        return $domain . '/' . $product->getSeoUrls()->first()->getSeoPathInfo();
    }
}
