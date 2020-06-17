<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data;

use Omikron\FactFinder\Shopware6\Export\ExportProducts;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DataProvider implements DataProviderInterface
{
    /** @var SalesChannelContext */
    private $context;

    /** @var ExportProducts */
    private $products;

    public function __construct(SalesChannelContext $context, ExportProducts $products)
    {
        $this->context  = $context;
        $this->products = $products;
    }

    /**
     * @inheritDoc
     */
    public function getEntities(): iterable
    {
        foreach ($this->products->getByContext($this->context) as $product) {
            yield from (new Entity\ProductEntity($product))->getEntities();
        }
    }
}
