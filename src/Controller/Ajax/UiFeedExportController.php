<?php


declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Controlelr\Ajax;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"administration"})
 */
class UiFeedExportController extends AbstractController
{
    /**
     * @Route("/fact-finder/export-feed", name="administration.fact_finder.export_feed", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     * @param Request $request
     */
    public function generateExportFeedAction(Request $request)
    {

    }
}
