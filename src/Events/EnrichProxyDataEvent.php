<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Events;

use Symfony\Contracts\EventDispatcher\Event;

class EnrichProxyDataEvent extends Event
{
    public function __construct(private array $data)
    {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
