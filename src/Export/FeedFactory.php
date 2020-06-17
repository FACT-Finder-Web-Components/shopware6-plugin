<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Export\Data\DataProvider;
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

    public function __construct(ExportProducts $products, FilterInterface $filter)
    {
        $this->products = $products;
        $this->filter   = $filter;
    }

    public function create(SalesChannelContext $context): Feed
    {
        return new Feed(new DataProvider($context, $this->products), $this->filter);
    }
}
