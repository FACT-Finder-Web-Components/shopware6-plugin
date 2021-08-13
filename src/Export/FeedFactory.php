<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use InvalidArgumentException;
use Omikron\FactFinder\Shopware6\Export\Data\DataProvider;
use Omikron\FactFinder\Shopware6\Export\Data\Factory\CompositeFactory;
use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * @SuppressWarnings(PHPMD.MissingImport)
 */
class FeedFactory
{
    /** ExportInterface[] */
    private array $exportEntities;
    private FilterInterface $filter;
    private CompositeFactory $compositeFactory;

    public function __construct(iterable $exportedEntities, FilterInterface $filter, CompositeFactory $compositeFactory)
    {
        $this->filter           = $filter;
        $this->compositeFactory = $compositeFactory;
        $this->exportEntities   = iterator_to_array($exportedEntities);
    }

    public function create(SalesChannelContext $context, string $exportType): Feed
    {
        $exporter = $this->first(array_filter(($this->exportEntities), fn (ExportInterface $exp): bool => $exp->getEntityType() === $exportType));
        if (!$exporter instanceof ExportInterface) {
            throw new InvalidArgumentException(sprintf('There is no exporter for given type: %s', $exportType));
        }

        return new Feed(new DataProvider($context, $exporter, $this->compositeFactory), $this->filter);
    }

    private function first(array $arr): ?ExportInterface
    {
        return empty($arr) ? null : reset($arr);
    }
}
