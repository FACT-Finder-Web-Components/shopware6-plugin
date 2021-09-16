<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Api;

use Omikron\FactFinder\Shopware6\Command\DataExportCommand;
use Omikron\FactFinder\Shopware6\Message\FeedExport;
use Omikron\FactFinder\Shopware6\MessageQueue\FeedExportHandler;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class UiFeedExportController extends AbstractController
{
    private FeedExportHandler $feedExportHandler;

    /**
     * UiFeedExportController constructor.
     *
     * @param FeedExportHandler $feedExportHandler
     */
    public function __construct(FeedExportHandler $feedExportHandler)
    {
        $this->feedExportHandler = $feedExportHandler;
    }

    /**
     * @Route("/api/_action/fact-finder/generate-feed", name="api.action.fact_finder.export_feed", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function generateExportFeedAction(Request $request): JsonResponse
    {
        $this->feedExportHandler->handle(new FeedExport(
            $request->query->get('salesChannelValue'),
            $request->query->get('salesChannelLanguageValue'),
            $request->query->get('exportTypeValue')
        ));

        return new JsonResponse();
    }

    /**
     * @Route("/api/_action/fact-finder/get-export-type-options", name="api.action.fact_finder.get_export_type_options", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function getTypeEntityMap(Request $request): JsonResponse
    {
        return new JsonResponse(array_merge(DataExportCommand::getBaseTypeEntityMap(), $this->container->getParameter('factfinder.data_export.entity_type_map')));
    }
}
