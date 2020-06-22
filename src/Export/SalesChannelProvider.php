<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Psr\Container\ContainerInterface;
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

class SalesChannelProvider
{
    /** @var EntityRepositoryInterface */
    private $salesChannelRepository;

    /** @var SalesChannelContextFactory */
    private $salesChannelContextFactory;

    /** @var SalesChannelContext */
    private $cachedChannel;

    public function __construct(
        EntityRepositoryInterface $salesChannelRepository,
        SalesChannelContextFactory $salesChannelContextFactory
    ) {
        $this->salesChannelRepository     = $salesChannelRepository;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        if (!$this->cachedChannel) {
            $salesChannel         = $this->getSalesChannel();
            $this->channelInCache = $this->salesChannelContextFactory->create('', $salesChannel->getId(), [
                SalesChannelContextService::LANGUAGE_ID => $salesChannel->getLanguageId(),
            ]);
        }

        return $this->cachedChannel;
    }

    private function getSalesChannel(): SalesChannelEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));
        $criteria->addAssociation('domains');
        return $this->salesChannelRepository->search($criteria, new Context(new SystemSource()))->first();
    }
}
