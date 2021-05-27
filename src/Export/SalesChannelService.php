<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\BackwardCompatibility\Extension\SalesChannelContextFactoryInterface;
use Shopware\Core\Checkout\Cart\CartRuleLoader;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class SalesChannelService
{
    /** @var EntityRepositoryInterface */
    private $channelRepository;

    /** @var SalesChannelContextFactoryInterface */
    private $channelContextFactory;

    /** @var CartRuleLoader */
    private $cartRuleLoader;

    /** @var SalesChannelContext|null */
    private $cachedContext;

    public function __construct(
        SalesChannelContextFactoryInterface $channelContextFactory,
        EntityRepositoryInterface $channelRepository,
        CartRuleLoader $cartRuleLoader
    ) {
        $this->channelRepository     = $channelRepository;
        $this->channelContextFactory = $channelContextFactory;
        $this->cartRuleLoader        = $cartRuleLoader;
    }

    public function getSalesChannelContext(SalesChannelEntity $salesChannel = null, $languageId = null): SalesChannelContext
    {
        if (!$this->cachedContext) {
            $usedChannel         = $salesChannel ?: $this->getDefaultStoreFrontSalesChannel();
            $this->cachedContext = $this->channelContextFactory->create('', $usedChannel->getId(), [
                SalesChannelContextService::LANGUAGE_ID => $languageId ?: $usedChannel->getLanguageId(),
            ]);
        }
        $this->cartRuleLoader->loadByToken($this->cachedContext, Random::getAlphanumericString(32));
        return $this->cachedContext;
    }

    private function getDefaultStoreFrontSalesChannel(): SalesChannelEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));
        $criteria->addAssociation('domains');
        return $this->channelRepository->search($criteria, new Context(new SystemSource()))->first();
    }
}
