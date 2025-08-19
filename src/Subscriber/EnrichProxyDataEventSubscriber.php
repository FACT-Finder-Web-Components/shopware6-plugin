<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Events\EnrichProxyDataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EnrichProxyDataEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [EnrichProxyDataEvent::class => 'enrichData'];
    }

    public function enrichData(EnrichProxyDataEvent $event): void
    {
        $data                 = $event->getData();
        $data['example_data'] = [
            'some_data'  => 'data_1moja',
            'some_data2' => 'data_2',
        ];
        $event->setData($data);
    }
}
