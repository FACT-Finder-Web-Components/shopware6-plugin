<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\DomainService;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class ImageUrl implements FieldInterface
{
    /** @var DomainService */
    private $domainService;

    public function __construct(DomainService $domainService)
    {
        $this->domainService = $domainService;
    }

    public function getName(): string
    {
        return 'ImageUrl';
    }

    public function getValue(Product $product): string
    {
        return $product->getCover()->getMedia()->getUrl();
    }
}
