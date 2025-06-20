<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Message;

readonly class FeedExport
{
    public function __construct(
        private ?string $salesChannelId = null,
        private ?string $salesChannelLanguageId = null,
        private ?string $exportTypeValue = null,
    ) {
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
