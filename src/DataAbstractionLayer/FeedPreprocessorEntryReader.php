<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\DataAbstractionLayer;

use Omikron\FactFinder\Shopware6\Export\FeedPreprocessorEntry;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class FeedPreprocessorEntryReader
{
    private SalesChannelService $channelService;
    private EntityRepositoryInterface $feedPreprocessorEntryRepository;

    public function __construct(
        SalesChannelService $channelService,
        EntityRepositoryInterface $feedPreprocessorEntryRepository
    ) {
        $this->channelService = $channelService;
        $this->feedPreprocessorEntryRepository = $feedPreprocessorEntryRepository;
    }

    /**
     * @return FeedPreprocessorEntry[]
     */
    public function read(string $productNumber, string $languageId = ''): array
    {
        $context = $this->channelService->getSalesChannelContext()->getContext();

        if ($languageId === '') {
            $languageId = $context->getLanguageId();
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('parentProductNumber', $productNumber));
//        $criteria->addFilter(new EqualsFilter('languageId', $languageId));
        $preprocessedFeeds = $this->feedPreprocessorEntryRepository->search($criteria, $context)->getElements();

        return array_reduce($preprocessedFeeds, function (array $acc, FeedPreprocessorEntry $preprocessedFeed) {
            $number = $preprocessedFeed->getProductNumber();

            if ($number !== null) {
                $acc[$number] = $preprocessedFeed;
            }

            return $acc;
        }, []);
    }
}
