<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Exception\DetectRedirectCampaignException;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\SearchAdapter;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Template\Engine;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Template\RecordList;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CategoryPageResponseSubscriber implements EventSubscriberInterface
{
    private Communication $config;
    private SearchAdapter $searchAdapter;
    private Engine $mustache;

    public function __construct(
        Communication $config,
        SearchAdapter $searchAdapter,
        Engine $mustache,
    ) {
        $this->config                 = $config;
        $this->searchAdapter          = $searchAdapter;
        $this->mustache               = $mustache;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onPageRendered',
        ];
    }

    public function onPageRendered(ResponseEvent $event): void
    {
        $request      = $event->getRequest();
        $response     = $event->getResponse();
        $categoryPath = $this->getCategoryPath($request);

        if ($response->getContent() === false) {
            return;
        }

        if (
            $this->config->isSsrActive() === false
            || $request->isXmlHttpRequest()
            || $categoryPath === ''
        ) {
            $response->setContent(str_replace('{FF_SEARCH_RESULT}', '{}', $response->getContent()));

            return;
        }

        $recordList = new RecordList(
            $request,
            $this->mustache,
            $this->searchAdapter,
            $request->attributes->get('sw-sales-channel-id'),
            $response->getContent(),
        );

        try {
            $response->setContent(
                $recordList->getContent(
                    $this->getParamsString($categoryPath),
                    true
                )
            );
        } catch (DetectRedirectCampaignException $exception) {
            $event->setResponse(new RedirectResponse($exception->getRedirectUrl()));
        }


    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getParamsString(string $categoryPath): string
    {
        $params = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);

        if ($params === '') {
            return $categoryPath;
        }

        return sprintf('%s&%s', $params, $categoryPath);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function getCategoryPath(Request $request): string
    {
        $categoryId = $this->getCategoryId($request);

        if ($categoryId === '') {
            return '';
        }

        return $request->attributes->get('categoryPath', '');
    }

    private function getCategoryId(Request $request): string
    {
        $uri   = $request->attributes->get('resolved-uri');
        $start = '/navigation/';
        $pos   = strpos($uri ?? '', $start);

        if ($pos !== 0) {
            return '';
        }

        return trim(substr($uri, strlen($start), strlen($uri)), '/');
    }
}
