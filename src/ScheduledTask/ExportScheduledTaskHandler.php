<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\ScheduledTask;

use Omikron\FactFinder\Shopware6\Message\FeedExport;
use Omikron\FactFinder\Shopware6\MessageQueue\FeedExportHandler;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: ExportScheduledTask::class)]
class ExportScheduledTaskHandler extends ScheduledTaskHandler
{
    public function __construct(
        EntityRepository $scheduledTaskRepository,
        private readonly LoggerInterface $factfinderLogger,
        private readonly SystemConfigService $systemConfig,
        private readonly FeedExportHandler $feedExportHandler,
    ) {
        parent::__construct($scheduledTaskRepository, $factfinderLogger);
    }

    public function run(): void
    {
        $autoExportEnabled = $this->systemConfig->get('OmikronFactFinder.config.autoExportEnabled');
        $salesChannel      = $this->systemConfig->get('OmikronFactFinder.config.autoExportSelectedSalesChannel');
        $language          = $this->systemConfig->get('OmikronFactFinder.config.autoExportSelectedLanguage');

        if (!$autoExportEnabled || !$salesChannel || !$language) {
            $this->factfinderLogger->info('FactFinder auto export is disabled or not configured properly.');

            return;
        }

        try {
            $this->feedExportHandler->__invoke(new FeedExport(
                $salesChannel,
                $language,
                'products'
            ));
        } catch (\Exception $e) {
            $this->factfinderLogger->error($e->getMessage());
        }
    }
}
