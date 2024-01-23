<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class MarketplaceReportTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'factfinder.marketplace_report_task';
    }

    public static function getDefaultInterval(): int
    {
        return 2592000; // 1 month in seconds
    }
}
