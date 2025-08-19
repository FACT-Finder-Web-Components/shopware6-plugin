<?php
declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class ExportScheduledTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'factfinder.auto_export_task';
    }

    public static function getDefaultInterval(): int
    {
        // change this value to adjust the export interval. This value is in seconds.
        // For example, to set the interval to 48 hours = 172800
        return 172800;
    }
}
