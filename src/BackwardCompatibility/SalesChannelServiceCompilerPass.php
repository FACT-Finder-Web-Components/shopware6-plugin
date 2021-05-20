<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\BackwardCompatibility;

use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SalesChannelServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $salesChannelServiceDefinition = $container->getDefinition(SalesChannelService::class);

        if ($container->has('Shopware\Core\System\SalesChannel\Context\CachedSalesChannelContextFactory')) {
            $salesChannelContextFactory = new Definition('Omikron\FactFinder\Shopware6\BackwardCompatibility\Extension\CachedSalesChannelContextFactory', $container->getDefinition('Shopware\Core\System\SalesChannel\Context\CachedSalesChannelContextFactory')->getArguments());
        } else {
            $salesChannelContextFactory = new Definition('Omikron\FactFinder\Shopware6\BackwardCompatibility\Extension\SalesChannelContextFactory', $container->getDefinition('Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory')->getArguments());
        }

        $salesChannelServiceDefinition->setArgument('$channelContextFactory', $salesChannelContextFactory);
    }
}
