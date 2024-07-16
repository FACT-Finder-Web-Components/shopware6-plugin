<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

interface NamingStrategyInterface
{
    public function createFeedFileName(string $exportType, string $channelId): string;
}
