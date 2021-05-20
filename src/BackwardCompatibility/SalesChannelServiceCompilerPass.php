<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\BackwardCompatibility;

use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class SalesChannelServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $salesChannelService   = $container->getDefinition(SalesChannelService::class);
        $channelContextFactory = new Definition('Omikron\FactFinder\Shopware6\BackwardCompatibility\Extension\SalesChannelContextFactory', $container->getDefinition('Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory')->getArguments());

        if ($container->has('Shopware\Core\System\SalesChannel\Context\CachedSalesChannelContextFactory')) {
            $channelContextFactory = new Definition('Omikron\FactFinder\Shopware6\BackwardCompatibility\Extension\CachedSalesChannelContextFactory', $container->getDefinition('Shopware\Core\System\SalesChannel\Context\CachedSalesChannelContextFactory')->getArguments());
        }

        $salesChannelService->setArgument('$channelContextFactory', $channelContextFactory);
    }
}
