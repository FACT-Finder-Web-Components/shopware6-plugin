<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Storefront\Controller;

use Omikron\FactFinder\Communication\Client\ClientException;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Exception\DetectRedirectCampaignException;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\SearchAdapter;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Template\Engine;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Template\RecordList;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ResultController extends StorefrontController
{
    private GenericPageLoader $pageLoader;
    private Communication $config;
    private LoggerInterface $factfinderLogger;

    public function __construct(
        Communication $config,
        GenericPageLoader $pageLoader,
        LoggerInterface $factfinderLogger,
    ) {
        $this->pageLoader = $pageLoader;
        $this->config     = $config;

        $this->factfinderLogger = $factfinderLogger;
    }

    /**
     * @Route(path="/factfinder/result", name="frontend.factfinder.result", methods={"GET"})
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function result(
        Request $request,
        SalesChannelContext $context,
        SearchAdapter $searchAdapter,
        Engine $mustache,
    ): Response {
        if ($this->config->disableFFWebc($context->getSalesChannelId()) === true) {
            throw new PageNotFoundException('WebComponents are disabled for this sales channel.');
        }

        $page     = $this->pageLoader->load($request, $context);
        $response = $this->renderStorefront('@Parent/storefront/page/factfinder/result.html.twig', ['page' => $page]);

        if ($this->config->isSsrActive() === false) {
            return $response;
        }

        $recordList = new RecordList(
            $request,
            $mustache,
            $searchAdapter,
            $context->getSalesChannelId(),
            $response->getContent(),
        );

        try {
            $response->setContent(
                $recordList->getContent(
                    $this->parseQueryString($request->getQueryString() ?? '', $request)
                )
            );
        } catch (DetectRedirectCampaignException $exception) {
            return new RedirectResponse($exception->getRedirectUrl());
        } catch (ClientException $e) {
            $this->factfinderLogger->error("{$e->getMessage()}. Check logs for more information.");
        }

        return $response;
    }

    private function parseQueryString(string $queryString, Request $request): string
    {
        if ($queryString === '') {
            return '';
        }

        $queryParams   = explode('&', $queryString);
        $queryParams[] = sprintf('sid=%s', $request->cookies->get('ffwebc_sid', ''));
        $result        = array_reduce(
            $queryParams,
            function (string $carry, string $queryParam) {
                $result = explode('=', $queryParam);

                return sprintf('%s&%s=%s', $carry, $result[0], htmlspecialchars($result[1]));
            },
            ''
        );

        return substr($result, 1);
    }
}
