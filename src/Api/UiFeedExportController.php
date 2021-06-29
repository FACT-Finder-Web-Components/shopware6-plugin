<?php


declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Api;

use Omikron\FactFinder\Shopware6\Message\FeedExport;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class UiFeedExportController extends AbstractController
{
    /**
     * @Route("/api/_action/fact-finder/generate-feed", name="api.action.fact_finder.export_feed", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     * @param MessageBusInterface $messageBus
     * @param Request $request
     */
    public function generateExportFeedAction(MessageBusInterface $messageBus, Request $request): JsonResponse
    {
        $messageBus->dispatch(new FeedExport(
            $request->query->get('this.salesChannelValue'),
            $request->query->get('salesChannelLanguageValue')
        ));

        return new JsonResponse();
    }
}
