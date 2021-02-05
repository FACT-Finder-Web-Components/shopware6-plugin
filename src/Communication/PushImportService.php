<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Communication;

use Omikron\FactFinder\Communication\Resource\Import;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Config\FtpConfig;
use Omikron\FactFinder\Shopware6\Exception\ImportRunningException;
use Psr\Http\Client\ClientExceptionInterface;

class PushImportService
{
    /** @var Communication */
    private $communicationConfig;

    /** @var FtpConfig */
    private $uploadConfig;

    /** @var Import */
    private $importAdapter;

    public function __construct(Import $importAdapter, Communication $communicationConfig, FtpConfig $uploadConfig)
    {
        $this->communicationConfig = $communicationConfig;
        $this->uploadConfig        = $uploadConfig;
        $this->importAdapter       = $importAdapter;
    }

    /**
     * @throws ImportRunningException|ClientExceptionInterface
     */
    public function execute(): void
    {
        $channel = $this->communicationConfig->getChannel();
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
