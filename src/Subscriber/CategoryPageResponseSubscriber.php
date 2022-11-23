<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Utilites\Ssr\SearchAdapter;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Template\RecordList;
use Shopware\Core\Framework\Event\BeforeSendResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class CategoryPageResponseSubscriber implements EventSubscriberInterface
{
    private SearchAdapter $searchAdapter;
    private Environment $twig;

    public function __construct(
        SearchAdapter $searchAdapter,
        Environment $twig
    ) {
        $this->searchAdapter = $searchAdapter;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeSendResponseEvent::class => 'onPageRendered',
        ];
    }

    public function onPageRendered(BeforeSendResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->attributes->get('isCategoryPage', false)) {
            return;
        }

        $response = $event->getResponse();
        $recordList = new RecordList(
            $this->twig,
            $this->searchAdapter,
            $response->getContent(),
        );
        $response->setContent(
            $recordList->getContent(
                $request->query->get('query'),
                true
            )
        );
    }
}
