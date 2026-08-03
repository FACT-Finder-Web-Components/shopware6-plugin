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
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
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
        $userId        = $request->cookies->get('ff_atlas_ai_user_id', $request->cookies->get('ff_user_id', ''));

        if ($userId !== '') {
            $queryParams[] = sprintf('userId=%s', $userId);
        }

        $result = array_reduce(
            $queryParams,
            function (string $carry, string $queryParam) {
                $result = explode('=', $queryParam, 2);
                $key    = $result[0];
                $value  = isset($result[1]) ? htmlspecialchars($result[1]) : '';

                return sprintf('%s&%s=%s', $carry, $key, $value);
            },
            ''
        );

        return substr($result, 1);
    }
}
