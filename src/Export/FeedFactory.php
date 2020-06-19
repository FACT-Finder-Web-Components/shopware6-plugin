<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Export\Data\DataProvider;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class FeedFactory
{
    /** @var FilterInterface */
    private $filter;
    /**
     * @var ExportProducts
     */
    private $products;

    /** @var iterable|FieldInterface[] */
    private $productFields;

    public function __construct(ExportProducts $products, FilterInterface $filter, iterable $productFields)
    {
        $this->products         = $products;
        $this->filter           = $filter;
        $this->productFields    = $productFields;
    }

    public function create(SalesChannelContext $context): Feed
    {
        return new Feed(new DataProvider($context, $this->products, $this->productFields), $this->filter);
    }
}
