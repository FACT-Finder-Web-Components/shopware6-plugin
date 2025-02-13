<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Storefront\Controller;

use Omikron\FactFinder\Communication\Client\ClientException;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Exception\DetectRedirectException;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\SearchAdapter;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Template\Engine;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Template\RecordList;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class ResultController extends StorefrontController
{
    public function __construct(
        private readonly Communication $config,
        private readonly GenericPageLoader $pageLoader,
        private readonly LoggerInterface $factfinderLogger,
    ) {
    }

    #[Route('/factfinder/result', name: 'frontend.factfinder.result', methods: ['GET'])]
    public function result(
        Request $request,
        SalesChannelContext $context,
        SearchAdapter $searchAdapter,
        Engine $handlebars,
    ): Response {
        $page     = $this->pageLoader->load($request, $context);
        $response = $this->renderStorefront('@Parent/storefront/page/factfinder/result.html.twig', ['page' => $page]);

        if ($this->config->isSsrActive() === false) {
            return $response;
        }

        $recordList = new RecordList(
            $request,
            $handlebars,
            $searchAdapter,
            $this->config,
            $context->getSalesChannelId(),
            $response->getContent(),
        );

        try {
            $response->setContent(
                $recordList->getContent(
                    $this->parseQueryString($request->getQueryString() ?? '', $request)
                )
            );
        } catch (DetectRedirectException $e) {
            return new RedirectResponse($e->getRedirectUrl());
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
