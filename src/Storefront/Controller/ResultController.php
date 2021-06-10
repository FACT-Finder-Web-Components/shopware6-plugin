<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Storefront\Controller;

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

    public function __construct(GenericPageLoader $pageLoader)
    {
        $this->pageLoader = $pageLoader;
    }

    /**
     * @Route(path="/factfinder/result", name="frontend.factfinder.result", methods={"GET"})
     */
    public function result(Request $request, SalesChannelContext $context): Response
    {
        $page = $this->pageLoader->load($request, $context);
        return $this->renderStorefront('@Parent/storefront/page/factfinder/result.html.twig', ['page' => $page]);
    }
}
