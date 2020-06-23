<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\DomainService;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class Deeplink implements FieldInterface
{
    /** @var DomainService */
    private $domainService;

    public function __construct(DomainService $domainService)
    {
        $this->domainService = $domainService;
    }

    public function getName(): string
    {
        return 'Deeplink';
    }

    public function getValue(Product $product): string
    {
        return $this->domainService->getDomain()->getUrl() . '/' . $product->getSeoUrls()->first()->getSeoPathInfo();
    }
}
