<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\Communication\PushImportService;
use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\Export\Stream\ConsoleOutput;
use Omikron\FactFinder\Shopware6\Export\Stream\CsvFile;
use Omikron\FactFinder\Shopware6\Upload\UploadService;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Language\LanguageEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Traversable;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductExportCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private const UPLOAD_FEED_OPTION              = 'upload';
    private const PUSH_IMPORT_OPTION              = 'import';
    private const SALES_CHANNEL_ARGUMENT          = 'sales_channel';
    private const SALES_CHANNEL_LANGUAGE_ARGUMENT = 'language';

    /** @var SalesChannelService */
    private $channelService;

    /** @var FeedFactory */
    private $feedFactory;

    /** @var UploadService */
    private $uploadService;

    /** @var PushImportService */
    private $pushImportService;

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
        PushImportService $pushImportService,
        UploadService $uploadService,
        Traversable $productFields,
        EntityRepositoryInterface $languageRepository,
        EntityRepositoryInterface $channelRepository,
        CurrencyFieldsProvider $currencyFieldsProvider
    ) {
        parent::__construct('factfinder:export:products');
        $this->channelService         = $channelService;
        $this->feedFactory            = $feedFactory;
        $this->pushImportService      = $pushImportService;
        $this->uploadService          = $uploadService;
        $this->languageRepository     = $languageRepository;
        $this->channelRepository      = $channelRepository;
        $this->currencyFieldsProvider = $currencyFieldsProvider;
        $this->productFields          = iterator_to_array($productFields);
    }

    protected function configure()
    {
        $this->setDescription('Export articles feed.');
        $this->addOption(self::UPLOAD_FEED_OPTION, 'u', InputOption::VALUE_NONE, 'Should upload after exporting');
        $this->addOption(self::PUSH_IMPORT_OPTION, 'i', InputOption::VALUE_NONE, 'Should import after uploading');
        $this->addArgument(self::SALES_CHANNEL_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel');
        $this->addArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel language');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $salesChannel   = null;
        $salesChannelId = $input->getArgument(self::SALES_CHANNEL_ARGUMENT);
        if (!empty($salesChannelId)) {
            $salesChannel = $this->channelRepository->search(
                new Criteria([$salesChannelId]),
                new Context(new SystemSource())
            )->first();
        }

        /** @var LanguageEntity $selectedLanguage */
        $selectedLanguage = $this->languageRepository->search(
            new Criteria([$input->getArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT) ?: Defaults::LANGUAGE_SYSTEM]),
            new Context(new SystemSource())
        )->first();

        $feedService = $this->feedFactory->create($this->channelService->getSalesChannelContext($salesChannel, $selectedLanguage->getId()));
        $feedColumns = $this->getFeedColumns();

        if (!$input->getOption(self::UPLOAD_FEED_OPTION)) {
            $feedService->generate(new ConsoleOutput($output), $feedColumns);
            return 0;
        }

        $fileHandle = tmpfile();
        $feedService->generate(new CsvFile($fileHandle), $feedColumns);
        $this->uploadService->upload($fileHandle);
        $output->writeln('Feed has been succesfully uploaded');

        if ($input->getOption(self::PUSH_IMPORT_OPTION)) {
            $this->pushImportService->execute();
            $output->writeln('FACT-Finder import has been start');
        }

        return 0;
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
