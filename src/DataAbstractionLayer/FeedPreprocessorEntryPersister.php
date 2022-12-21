<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\DataAbstractionLayer;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class FeedPreprocessorEntryPersister
{
    private EntityRepository $entryRepository;

    public function __construct(EntityRepository $entryRepository)
    {
        $this->entryRepository = $entryRepository;
    }

    public function deleteAllProductEntries(string $productNumber, Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('parentProductNumber', $productNumber));
        $ids = array_map(fn (
            string $id): array => ['id' => $id], $this->entryRepository->searchIds($criteria, $context)->getIds());

        $this->entryRepository->delete($ids, $context);
    }

    /**
     * @param array[FeedPreprocessorEntry] $entries
     * @param Context $context
     */
    public function insertProductEntries(array $entries, Context $context): void
    {
        $this->entryRepository->create(array_values($entries), $context);
    }
}
