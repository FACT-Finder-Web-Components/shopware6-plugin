<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data;

use Omikron\FactFinder\Shopware6\Export\ExportProducts;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DataProvider implements DataProviderInterface
{
    /** @var SalesChannelContext */
    private $context;

    /** @var ExportProducts */
    private $products;

    /** @var iterable|FieldInterface[] */
    private $productFields;

    public function __construct(SalesChannelContext $context, ExportProducts $products, iterable $productFields)
    {
        $this->context       = $context;
        $this->products      = $products;
        $this->productFields = $productFields;
    }

    /**
     * @inheritDoc
     */
    public function getEntities(): iterable
    {
        foreach ($this->products->getByContext($this->context) as $product) {
            yield from (new Entity\ProductEntity($product, $this->productFields))->getEntities();
        }
    }
}
