<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use InvalidArgumentException;
use Omikron\FactFinder\Shopware6\Export\Data\DataProvider;
use Omikron\FactFinder\Shopware6\Export\Data\Factory\CompositeFactory;
use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Traversable;

/**
 * @SuppressWarnings(PHPMD.MissingImport)
 *
 * @api
 */
class FeedFactory
{
    /** ExportInterface[] */
    private array $exporters;
    private FilterInterface $filter;
    private CompositeFactory $compositeFactory;

    public function __construct(Traversable $exporters, FilterInterface $filter, CompositeFactory $compositeFactory)
    {
        $this->filter           = $filter;
        $this->compositeFactory = $compositeFactory;
        $this->exporters        = iterator_to_array($exporters);
    }

    public function create(SalesChannelContext $context, string $exportType): Feed
    {
        $exporter = $this->first(array_filter(($this->exporters), fn (ExportInterface $exp): bool => $exp->getCoveredEntityType() === $exportType));
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
