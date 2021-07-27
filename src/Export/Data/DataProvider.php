<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data;

use Omikron\FactFinder\Shopware6\Export\Data\Entity\EntityFactory;
use Omikron\FactFinder\Shopware6\Export\ExportProducts;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DataProvider implements DataProviderInterface
{
    private SalesChannelContext $context;
    private ExportProducts $products;
    private EntityFactory $entityFactory;

    public function __construct(SalesChannelContext $context, ExportProducts $products, EntityFactory $entityFactory)
    {
        $this->context       = $context;
        $this->products      = $products;
        $this->entityFactory = $entityFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities(): iterable
    {
        foreach ($this->products->getByContext($this->context) as $product) {
            yield from $this->entityFactory->createEntities($product);
        }
    }
}
