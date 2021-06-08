<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Export\Data\DataProviderInterface;
use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;
use Omikron\FactFinder\Shopware6\Export\Stream\StreamInterface;

class Feed
{
    /** @var DataProviderInterface */
    private $dataProvider;

    /** @var FilterInterface */
    private $filter;

    public function __construct(DataProviderInterface $dataProvider, FilterInterface $filter)
    {
        $this->dataProvider = $dataProvider;
        $this->filter       = $filter;
    }

    public function generate(StreamInterface $stream, array $columns): void
    {
        $stream->addEntity($columns);
        $emptyRecord = array_combine($columns, array_fill(0, count($columns), ''));
        foreach ($this->dataProvider->getEntities() as $entity) {
            $entityData = array_merge($emptyRecord, array_intersect_key($entity->toArray(), $emptyRecord));

//            dd(
//                $entity->toArray(),
//                $emptyRecord,
//                array_intersect_key($entity->toArray(), $emptyRecord),
//                $entityData
//            );

            $stream->addEntity($this->prepare($entityData));
        }
    }

    private function prepare(array $data): array
    {
        return array_map([$this->filter, 'filterValue'], $data);
    }
}
