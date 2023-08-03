<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Api;

use Omikron\FactFinder\Shopware6\Command\DataExportCommand;
use Omikron\FactFinder\Shopware6\Message\FeedExport;
use Omikron\FactFinder\Shopware6\Message\RefreshExportCache;
use Omikron\FactFinder\Shopware6\MessageQueue\FeedExportHandler;
use Omikron\FactFinder\Shopware6\MessageQueue\RefreshExportCacheHandler;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
class UiFeedExportController extends AbstractController
{
    public function __construct(
        private FeedExportHandler $feedExportHandler,
        private DataExportCommand $dataExportCommand,
        private RefreshExportCacheHandler $refreshCacheHandler
    ) {
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
        $this->feedExportHandler->__invoke(new FeedExport(
            $request->query->get('salesChannelValue'),
            $request->query->get('salesChannelLanguageValue'),
            $request->query->get('exportTypeValue')
        ));

        return new JsonResponse();
    }

    /**
     * @Route("/api/_action/fact-finder/get-export-type-options", name="api.action.fact_finder.get_export_type_options", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     *
     * @return JsonResponse
     */
    public function getTypeEntityMap(): JsonResponse
    {
        return new JsonResponse($this->dataExportCommand->getTypeEntityMap());
    }

    /**
     * @Route("/api/_action/fact-finder/refresh-export-cache", name="api.action.fact_finder.refresh-export-cache", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function refreshExportCacheAction(Request $request): JsonResponse
    {
        $this->refreshCacheHandler->__invoke(new RefreshExportCache(
            $request->query->get('salesChannelValue'),
            $request->query->get('salesChannelLanguageValue')
        ));

        return new JsonResponse();
    }
}
