<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\BackwardCompatibility\Extension;

use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory as SalesChannelContextFactory_Core;

class SalesChannelContextFactory extends SalesChannelContextFactory_Core implements SalesChannelContextFactoryInterface
{
}
