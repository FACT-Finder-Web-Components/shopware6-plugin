<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class SalesChannelService
{
    /** @var EntityRepositoryInterface */
    private $salesChannelRepository;

    /** @var SalesChannelContextFactory */
    private $salesChannelContextFactory;

    /** @var SalesChannelContext|null */
    private $cachedSalesChannel;

    public function __construct(
        EntityRepositoryInterface $salesChannelRepository,
        SalesChannelContextFactory $salesChannelContextFactory
    ) {
        $this->salesChannelRepository     = $salesChannelRepository;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
    }

    public function getSalesChannelContext(SalesChannelEntity $salesChannel = null): SalesChannelContext
    {
        if (!$this->cachedSalesChannel) {
            $usedChannel              = $salesChannel ?: $this->getDefaultStoreFrontSalesChannel();
            $this->cachedSalesChannel = $this->salesChannelContextFactory->create('', $usedChannel->getId(), [
                SalesChannelContextService::LANGUAGE_ID => $usedChannel->getLanguageId(),
            ]);
        }

        return $this->cachedSalesChannel;
    }

    private function getDefaultStoreFrontSalesChannel(): SalesChannelEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));
        $criteria->addAssociation('domains');
        return $this->salesChannelRepository->search($criteria, new Context(new SystemSource()))->first();
    }
}
