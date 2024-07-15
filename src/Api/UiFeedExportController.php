<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Api;

use Omikron\FactFinder\Shopware6\Command\DataExportCommand;
use Omikron\FactFinder\Shopware6\Message\FeedExport;
use Omikron\FactFinder\Shopware6\MessageQueue\FeedExportHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route(defaults: ['_routeScope' => ['api']])]
class UiFeedExportController extends AbstractController
{
    public function __construct(
        private readonly FeedExportHandler $feedExportHandler,
        private readonly DataExportCommand $dataExportCommand,
        private readonly LoggerInterface $factfinderLogger
    ) {
    }

    #[Route(
        '/api/_action/fact-finder/generate-feed',
        name: 'api.action.fact_finder.export_feed',
        defaults: ['XmlHttpRequest' => true],
        methods: ['GET']
    )]
    public function generateExportFeedAction(Request $request): JsonResponse
    {
        try {
            $this->feedExportHandler->__invoke(new FeedExport(
                $request->query->get('salesChannelValue'),
                $request->query->get('salesChannelLanguageValue'),
                $request->query->get('exportTypeValue')
            ));

            return new JsonResponse();
        } catch (\Exception $e) {
            $this->factfinderLogger->error($e->getMessage());

            return new JsonResponse(['message' => 'Problem with export. Check logs for more informations'], 400);
        }
    }

    #[Route(
        '/api/_action/fact-finder/get-export-type-options',
        name: 'api.action.fact_finder.get_export_type_options',
        defaults: ['XmlHttpRequest' => true],
        methods: ['GET']
    )]
    public function getTypeEntityMap(): JsonResponse
    {
        return new JsonResponse($this->dataExportCommand->getTypeEntityMap());
    }
}
