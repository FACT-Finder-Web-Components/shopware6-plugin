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
        if ($container->has('Shopware\Core\System\SalesChannel\Context\CachedSalesChannelContextFactory')) {
            $salesChannelService  = new Definition('Omikron\FactFinder\Shopware6\BackwardCompatibility\Extension\CachedSalesChannelContextFactoryExtension', [
                new Reference('Shopware\Core\System\SalesChannel\Context\CachedSalesChannelContextFactory'),
                new Reference('tag_aware_cache'),
                new Reference('Shopware\Core\Framework\Adapter\Cache\CacheTracer'),
                new Reference('logger'),
            ]);

            $container->getDefinition(SalesChannelService::class)->setArgument('$channelContextFactory', $salesChannelService);
        }
    }
}
