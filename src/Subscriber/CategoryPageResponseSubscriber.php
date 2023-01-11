<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Exception;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\SearchAdapter;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Template\RecordList;
use Shopware\Core\Framework\Event\BeforeSendResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class CategoryPageResponseSubscriber implements EventSubscriberInterface
{
    private SearchAdapter $searchAdapter;
    private Environment $twig;
    private Communication $config;

    public function __construct(
        Communication $config,
        SearchAdapter $searchAdapter,
        Environment $twig
    ) {
        $this->config        = $config;
        $this->searchAdapter = $searchAdapter;
        $this->twig          = $twig;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeSendResponseEvent::class => 'onPageRendered',
        ];
    }

    public function onPageRendered(BeforeSendResponseEvent $event): void
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        try {
            $categoryPath = $request->getSession()->get(CategoryPageSubscriber::CATEGORY_PATH, '');
        } catch (Exception $e) {
            $categoryPath = '';
        }

        if (
            $this->config->isSsrActive() === false
            || $request->isXmlHttpRequest()
            || $categoryPath === ''
        ) {
            $response->setContent(str_replace('{FF_SEARCH_RESULT}', '', $response->getContent()));

            return;
        }

        $recordList = new RecordList(
            $this->twig,
            $this->searchAdapter,
            $request->attributes->get('sw-sales-channel-id'),
            $response->getContent(),
        );
        $response->setContent(
            $recordList->getContent(
                $categoryPath,
                true
            )
        );
    }
}
