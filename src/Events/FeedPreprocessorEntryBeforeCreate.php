<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Events;

use Omikron\FactFinder\Shopware6\Export\FeedPreprocessorEntry;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\ShopwareEvent;
use Symfony\Contracts\EventDispatcher\Event;

class FeedPreprocessorEntryBeforeCreate extends Event implements ShopwareEvent
{
    private FeedPreprocessorEntry $feedPreprocessorEntry;
    private Context $context;

    public function __construct(FeedPreprocessorEntry $entry, Context $context)
    {
        $this->entry   = $entry;
        $this->context = $context;
    }

    public function getEntry(): FeedPreprocessorEntry
    {
        return $this->entry;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
