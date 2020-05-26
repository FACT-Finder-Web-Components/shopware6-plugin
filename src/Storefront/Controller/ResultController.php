<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Storefront\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class ResultController extends StorefrontController
{
    /**
     * @Route(path="/result", name="result", methods={"GET"})
     */
    public function result(Request $request, SalesChannelContext $context): Response
    {
        return $this->renderStorefront('@Storefront/storefront/page/factfinder/result.html.twig');
    }
}
