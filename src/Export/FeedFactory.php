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
    public const CMS_EXPORT_TYPE     = 'cms';
    public const BRAND_EXPORT_TYPE     = 'manufacturer';

    private FilterInterface $filter;
    private ExportProducts $products;
    private EntityFactory $entityFactory;
    private ExportCategories $categories;
    private ExportBrands $brands;

    public function __construct(ExportProducts $products, FilterInterface $filter, EntityFactory $entityFactory, ExportCategories $categories, ExportBrands $brands)
    {
        $this->products      = $products;
        $this->filter        = $filter;
        $this->entityFactory = $entityFactory;
        $this->categories    = $categories;
        $this->brands        = $brands;
    }

    public function create(SalesChannelContext $context, string $exportType): Feed
    {
        switch ($exportType) {
            case self::BRAND_EXPORT_TYPE:
                $exportData = $this->brands;

                break;
            case self::PRODUCT_EXPORT_TYPE:
                $exportData = $this->products;

                break;
            case self::CMS_EXPORT_TYPE:
                $exportData = $this->categories;

                break;
            default:
                throw new \Exception('Unknown export type: ' . $exportType);
        }

        return new Feed(new DataProvider($context, $exportData, $this->entityFactory), $this->filter);
    }
}
