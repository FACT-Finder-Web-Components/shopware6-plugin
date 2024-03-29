<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Events;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\ShopwareEvent;
use Symfony\Contracts\EventDispatcher\Event;

class FeedPreprocessorEntryBeforeCreate extends Event implements ShopwareEvent
{
    private array $entry;
    private Context $context;

    public function __construct(array $entry, Context $context)
    {
        $this->entry   = $entry;
        $this->context = $context;
    }

    public function getEntry(): array
    {
        return $this->entry;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function setEntry(array $entry): void
    {
        $this->entry = $entry;
    }
}
