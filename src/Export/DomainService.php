<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Shopware\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainEntity;

class DomainService
{
    /** @var SalesChannelService */
    private $channelService;

    public function __construct(SalesChannelService $channelService)
    {
        $this->channelService = $channelService;
    }

    public function getDomain(): SalesChannelDomainEntity
    {
        return $this->channelService->getSalesChannelContext()->getSalesChannel()->getDomains()->first();
    }
}
