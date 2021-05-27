<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Config\ExcludedFields;
use Omikron\FactFinder\Shopware6\Export\Data\DataProvider;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\EntityFactory;
use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class FeedFactory
{
    /** @var FilterInterface */
    private $filter;

    /** @var ExportProducts */
    private $products;

    /** @var EntityFactory */
    private $entityFactory;

    public function __construct(ExportProducts $products, FilterInterface $filter, EntityFactory $entityFactory)
    {
        $this->products      = $products;
        $this->filter        = $filter;
        $this->entityFactory = $entityFactory;
    }

    public function create(SalesChannelContext $context): Feed
    {
        return new Feed(new DataProvider($context, $this->products, $this->entityFactory), $this->filter);
    }
}
