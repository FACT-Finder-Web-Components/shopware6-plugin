<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Message;

class FeedExport
{
    /** @var null | string */
    private $salesChannelId;

    /** @var null | string */
    private $salesChannelLanguageId;

    public function __construct(string $salesChannelId = null, string $salesChannelLanguageId = null)
    {
        $this->salesChannelId         = $salesChannelId;
        $this->salesChannelLanguageId = $salesChannelLanguageId;
    }

    public function getSalesChannelId(): ?string
    {
        return $this->salesChannelId;
    }

    public function getSalesChannelLanguageId(): ?string
    {
        return $this->salesChannelLanguageId;
    }
}
