<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Export\Data\DataProvider;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\EntityFactory;
use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class FeedFactory
{
    public const PRODUCT_EXPORT_TYPE = 'product';
    public const CMS_EXPORT_TYPE = 'cms';

    private FilterInterface $filter;
    private ExportProducts $products;
    private EntityFactory $entityFactory;
    private ExportCategories $categories;

    public function __construct(ExportProducts $products, FilterInterface $filter, EntityFactory $entityFactory, ExportCategories $categories)
    {
        $this->products      = $products;
        $this->filter        = $filter;
        $this->entityFactory = $entityFactory;
        $this->categories = $categories;
    }

    public function create(SalesChannelContext $context, string $exportType): Feed
    {
        return new Feed(new DataProvider($context, $exportType == self::CMS_EXPORT_TYPE ? $this->categories : $this->products, $this->entityFactory), $this->filter);
    }
}
