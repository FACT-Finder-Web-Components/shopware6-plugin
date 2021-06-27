<?php


declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Api;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\Export\Stream\CsvFile;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Traversable;

/**
 * @RouteScope(scopes={"api"})
 */
class UiFeedExportController extends AbstractController
{
    /** @var SalesChannelService */
    private $channelService;

    /** @var FeedFactory */
    private $feedFactory;

    /** @var FieldInterface[] */
    private $productFields;

    /** @var EntityRepositoryInterface */
    private $languageRepository;

    /** @var EntityRepositoryInterface */
    private $channelRepository;

    /** @var CurrencyFieldsProvider */
    private $currencyFieldsProvider;

    public function __construct(
        SalesChannelService $channelService,
        FeedFactory $feedFactory,
        Traversable $productFields,
        EntityRepositoryInterface $languageRepository,
        EntityRepositoryInterface $channelRepository,
        CurrencyFieldsProvider $currencyFieldsProvider
    ) {
        $this->channelService = $channelService;
        $this->feedFactory = $feedFactory;
        $this->productFields = iterator_to_array($productFields);
        $this->languageRepository = $languageRepository;
        $this->channelRepository = $channelRepository;
        $this->currencyFieldsProvider = $currencyFieldsProvider;
    }


    /**
     * @Route("/api/_action/fact-finder/generate-feed", name="api.action.fact_finder.export_feed", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     * @param Request $request
     */
    public function generateExportFeedAction(Request $request): StreamedResponse
    {
        $fileName = sprintf('export.%s.csv', 'test');
        $tmpPath = __DIR__ . '/../Resources/tmp';
        $feedService = $this->feedFactory->create($this->channelService->getSalesChannelContext());
        $feedColumns = $this->getFeedColumns();

        $filesystem = new Filesystem((new Local($tmpPath)));
        $fileHandle = tmpfile();
        $feedService->generate(new CsvFile($fileHandle), $feedColumns);
        $filesystem->putStream($fileName, $fileHandle);

        return new StreamedResponse(function () use($fileHandle) {
            fpassthru($fileHandle);
            exit();
        }, 200, [
            'Content-Transfer-Encoding', 'binary',
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            'Content-Length' => fstat($fileHandle)['size'],
        ]);
    }

    private function getFeedColumns(): array
    {
        $base   = (array) $this->container->getParameter('factfinder.export.columns.base');
        $fields = array_merge($this->productFields, $this->currencyFieldsProvider->getCurrencyFields());

        return array_values(array_unique(array_merge($base, array_map([$this, 'getFieldName'], $fields))));
    }

    private function getFieldName(FieldInterface $field): string
    {
        return $field->getName();
    }
}
