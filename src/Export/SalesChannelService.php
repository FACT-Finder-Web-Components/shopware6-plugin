<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Context\CachedSalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class SalesChannelService
{
    private EntityRepositoryInterface $channelRepository;
    private CachedSalesChannelContextFactory $channelContextFactory;
    private ?SalesChannelContext $cachedSalesChannel = null;

    public function __construct(
        CachedSalesChannelContextFactory $channelContextFactory,
        EntityRepositoryInterface $channelRepository
    ) {
        $this->channelRepository     = $channelRepository;
        $this->channelContextFactory = $channelContextFactory;
    }

    public function getSalesChannelContext(SalesChannelEntity $salesChannel = null, $languageId = null): SalesChannelContext
    {
        if (!$this->cachedSalesChannel) {
            $usedChannel              = $salesChannel ?: $this->getDefaultStoreFrontSalesChannel();
            $this->cachedSalesChannel = $this->channelContextFactory->create('', $usedChannel->getId(), [
                SalesChannelContextService::LANGUAGE_ID => $languageId ?: $usedChannel->getLanguageId(),
            ]);
        }

        return $this->cachedSalesChannel;
    }

    private function getDefaultStoreFrontSalesChannel(): SalesChannelEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));
        $criteria->addAssociation('domains');
        return $this->channelRepository->search($criteria, new Context(new SystemSource()))->first();
    }
}
