<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Storefront\Controller;

use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\SearchAdapter;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Template\RecordList;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class ResultController extends StorefrontController
{
    private GenericPageLoader $pageLoader;
    private Communication $config;

    public function __construct(
        Communication $config,
        GenericPageLoader $pageLoader
    ) {
        $this->pageLoader = $pageLoader;
        $this->config = $config;
    }

    /**
     * @Route(path="/factfinder/result", name="frontend.factfinder.result", methods={"GET"})
     */
    public function result(
        Request $request,
        SalesChannelContext $context,
        SearchAdapter $searchAdapter
    ): Response {
        $page = $this->pageLoader->load($request, $context);
        $response = $this->renderStorefront('@Parent/storefront/page/factfinder/result.html.twig', ['page' => $page]);

        if ($this->config->isSsrActive() === false) {
            return $response;
        }

        $recordList = new RecordList(
            $this->container->get('twig'),
            $searchAdapter,
            $response->getContent(),
        );
        $response->setContent(
            $recordList->getContent(
                $request->query->get('query', '')
            )
        );

        return $response;
    }
}
