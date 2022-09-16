<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\DataAbstractionLayer;

use Omikron\FactFinder\Shopware6\Export\FeedPreprocessorEntry;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class FeedPreprocessorEntryReader
{
    private SalesChannelService $channelService;
    private EntityRepositoryInterface $entryRepository;

    public function __construct(
        SalesChannelService $channelService,
        EntityRepositoryInterface $entryRepository
    ) {
        $this->channelService                  = $channelService;
        $this->entryRepository = $entryRepository;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return FeedPreprocessorEntry[]
     */
    public function read(string $productNumber, ?string $languageId): array
    {
        $context  = $this->channelService->getSalesChannelContext()->getContext();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('parentProductNumber', $productNumber));

        if ($languageId) {
            $criteria->addFilter(new EqualsFilter('languageId', Uuid::fromHexToBytes($context->getLanguageId())));
        }

        $preprocessedFeeds = $this->entryRepository->search($criteria, $context)->getElements();

        return array_reduce($preprocessedFeeds, function (array $acc, FeedPreprocessorEntry $preprocessedFeed) {
            $number = $preprocessedFeed->getProductNumber();

            if ($number !== null) {
                $acc[$number] = $preprocessedFeed;
            }

            return $acc;
        }, []);
    }
}
