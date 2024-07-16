<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

class ChannelTypeNamingStrategy implements NamingStrategyInterface
{
    public function createFeedFileName(string $exportType, string $channelId): string
    {
        if ($exportType === 'products') {
            return sprintf('export.productData.%s.csv', $channelId);
        }

        return sprintf('export.%s.%s.csv', $exportType, $channelId);
    }
}
