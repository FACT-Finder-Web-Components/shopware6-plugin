<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Message;

class FeedExport
{
    private ?string $salesChannelId;
    private ?string $salesChannelLanguageId;
    private ?string $exportTypeValue;

    public function __construct(string $salesChannelId = null, string $salesChannelLanguageId = null, string $exportTypeValue = null)
    {
        $this->salesChannelId         = $salesChannelId;
        $this->salesChannelLanguageId = $salesChannelLanguageId;
        $this->exportTypeValue = $exportTypeValue;
    }

    public function getSalesChannelId(): ?string
    {
        return $this->salesChannelId;
    }

    public function getSalesChannelLanguageId(): ?string
    {
        return $this->salesChannelLanguageId;
    }

    public function getExportTypeValue(): ?string
    {
        return $this->exportTypeValue;
    }
}
