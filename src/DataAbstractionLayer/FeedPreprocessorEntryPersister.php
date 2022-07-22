<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\DataAbstractionLayer;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class FeedPreprocessorEntryPersister
{
    private EntityRepositoryInterface $entryRepository;

    public function __construct(EntityRepositoryInterface $feedPreprocessorEntryRepository)
    {
        $this->entryRepository = $feedPreprocessorEntryRepository;
    }

    public function deleteAllProductEntries(string $productNumber, Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('parentProductNumber', $productNumber));
        $a = $this->entryRepository->search($criteria, $context)->getElements();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('parentProductNumber', $productNumber));
        $ids = array_map(fn(
            string $id): array => ['id' => $id], $this->entryRepository->searchIds($criteria, $context)->getIds());

        $this->entryRepository->delete($ids, $context);
    }

    /**
     * @param array[FeedPreprocessorEntry] $entries
     * @param Context $context
     */
    public function insertProductEntries(array $entries, Context $context): void
    {
        foreach ($entries as $entry) {
            $entryData = [
                'languageId'          => Uuid::fromHexToBytes($entry->getLanguageId()),
                'productNumber'       => $entry->getProductNumber(),
                'parentProductNumber' => $entry->getParentProductNumber(),
                'variationKey'        => $entry->getVariationKey(),
                'filterAttributes'    => $entry->getFilterAttributes(),
                'customFields'        => $entry->getCustomFields(),
                'additionalCache'     => $entry->getAdditionalCache()
            ];

            try {
                $this->entryRepository->create([$entryData], $context);
            } catch (\Throwable $e) {
                //@todo implement me
            }
        }
    }
}
