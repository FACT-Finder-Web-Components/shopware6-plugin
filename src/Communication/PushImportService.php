<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Communication;

use Omikron\FactFinder\Communication\Resource\Import;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Config\FtpConfig;
use Omikron\FactFinder\Shopware6\Exception\ImportRunningException;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Psr\Http\Client\ClientExceptionInterface;

class PushImportService
{
    private Communication $communicationConfig;
    private FtpConfig $uploadConfig;
    private Import $importAdapter;
    private SalesChannelService $salesChannelService;

    public function __construct(
        Import $importAdapter,
        Communication $communicationConfig,
        FtpConfig $uploadConfig,
        SalesChannelService $salesChannelService
    ) {
        $this->communicationConfig = $communicationConfig;
        $this->uploadConfig        = $uploadConfig;
        $this->importAdapter       = $importAdapter;
        $this->salesChannelService = $salesChannelService;
    }

    /**
     * @throws ImportRunningException|ClientExceptionInterface
     */
    public function execute(): void
    {
        $salesChannelId = $this->salesChannelService->getSalesChannelContext()->getSalesChannel()->getId();
        $channel        = $this->communicationConfig->getChannel($salesChannelId);
        $this->checkNotRunning($channel);
        foreach ($this->uploadConfig->getPushImportTypes() as $type) {
            $this->importAdapter->import($channel, $type);
        }
    }

    /**
     * @param string $channel
     *
     * @throws ImportRunningException|ClientExceptionInterface
     */
    private function checkNotRunning(string $channel): void
    {
        if ($this->importAdapter->running($channel)) {
            throw new ImportRunningException();
        }
    }
}
