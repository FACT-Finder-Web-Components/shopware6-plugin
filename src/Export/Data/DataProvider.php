<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data;

use Omikron\FactFinder\Shopware6\Export\Data\Entity\EntityFactory;
use Omikron\FactFinder\Shopware6\Export\ExportInterface;
use Omikron\FactFinder\Shopware6\Export\ExportProducts;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DataProvider implements DataProviderInterface
{
    private SalesChannelContext $context;
    private ExportInterface $exportData;
    private EntityFactory $entityFactory;

    public function __construct(SalesChannelContext $context, ExportInterface $exportData, EntityFactory $entityFactory)
    {
        $this->context       = $context;
        $this->exportData      = $exportData;
        $this->entityFactory = $entityFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities(): iterable
    {
        foreach ($this->exportData->getByContext($this->context) as $singleData) {
            yield from $this->entityFactory->createEntities($singleData);
        }
    }
}
