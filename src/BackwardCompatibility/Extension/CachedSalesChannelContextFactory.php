<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\BackwardCompatibility\Extension;

use Shopware\Core\System\SalesChannel\Context\CachedSalesChannelContextFactory as CachedSalesChannelContextFactory_Core;

class CachedSalesChannelContextFactory extends CachedSalesChannelContextFactory_Core implements SalesChannelContextFactoryInterface
{
}
