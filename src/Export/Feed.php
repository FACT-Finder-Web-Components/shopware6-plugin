<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Export\Data\DataProviderInterface;
use Omikron\FactFinder\Shopware6\Export\Data\Factory\CompositeFactory;
use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;
use Omikron\FactFinder\Shopware6\Export\Stream\StreamInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class Feed
{
    private SalesChannelContext$context;
    private ExportInterface $exporter;
    private CompositeFactory $compositeFactory;
    private FilterInterface $filter;

    public function __construct(SalesChannelContext $context, ExportInterface $exporter, CompositeFactory $compositeFactory, FilterInterface $filter)
    {
        $this->context = $context;
        $this->exporter = $exporter;
        $this->compositeFactory = $compositeFactory;
        $this->filter = $filter;
    }

    public function generate(StreamInterface $stream, array $columns): void
    {
        $stream->addEntity($columns);
        $emptyRecord = array_combine($columns, array_fill(0, count($columns), ''));

        foreach ($this->getEntities() as $entity) {
            $entityData = array_merge($emptyRecord, array_intersect_key($entity->toArray(), $emptyRecord));
            $stream->addEntity($this->prepare($entityData));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities(): iterable
    {
        foreach ($this->exporter->getByContext($this->context) as $entity) {
            yield from $this->compositeFactory->createEntities($entity, $this->exporter->getProducedExportEntityType());
        }
    }

    private function prepare(array $data): array
    {
        return array_map([$this->filter, 'filterValue'], $data);
    }
}
