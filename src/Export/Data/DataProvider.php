<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data;

use Omikron\FactFinder\Shopware6\Export\Data\Factory\FactoryInterface;
use Omikron\FactFinder\Shopware6\Export\ExportInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DataProvider implements DataProviderInterface
{
    private SalesChannelContext $context;
    private ExportInterface $exportData;
    private FactoryInterface $entityFactory;

    public function __construct(SalesChannelContext $context, ExportInterface $exportData, FactoryInterface $entityFactory)
    {
        $this->context         = $context;
        $this->exportData      = $exportData;
        $this->entityFactory   = $entityFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities(): iterable
    {
        foreach ($this->exportData->getByContext($this->context) as $entity) {
            yield from $this->entityFactory->createEntities($entity, $this->exportData->getProducedExportEntityType());
        }
    }
}
