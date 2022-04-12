<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

class ChannelTypeNamingStrategy implements NamingStrategyInterface
{
    public function createFeedFileName(...$parts): string
    {
        return sprintf('export.%s.%s.csv', ...$parts);
    }
}
