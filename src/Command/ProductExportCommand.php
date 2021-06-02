<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Doctrine\DBAL\Connection;
use Omikron\FactFinder\Shopware6\Communication\PushImportService;
use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\Field\Price;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\Export\Stream\ConsoleOutput;
use Omikron\FactFinder\Shopware6\Export\Stream\CsvFile;
use Omikron\FactFinder\Shopware6\Upload\UploadService;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\Currency\CurrencyEntity;
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

    private const UPLOAD_FEED_OPTION = 'upload';
    private const PUSH_IMPORT_OPTION = 'import';
    private const SALES_CHANNEL_ARGUMENT = 'sales_channel';
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

    /** @var EntityRepositoryInterface */
    private $currencyRepository;

    /** @var array | null  */
    private $currencyList = null;

    /** @var CurrencyEntity | null  */
    private $defaultCurrency = null;

    /** @var Connection  */
    private $connection;

    public function __construct(
        SalesChannelService $channelService,
        FeedFactory $feedFactory,
        PushImportService $pushImportService,
        UploadService $uploadService,
        Traversable $productFields,
        EntityRepositoryInterface $languageRepository,
        EntityRepositoryInterface $channelRepository,
        EntityRepositoryInterface $currencyRepository,
        Connection $connection
    ) {
        parent::__construct('factfinder:export:products');
        $this->channelService = $channelService;
        $this->feedFactory = $feedFactory;
        $this->pushImportService = $pushImportService;
        $this->uploadService = $uploadService;
        $this->languageRepository = $languageRepository;
        $this->channelRepository = $channelRepository;
        $this->currencyRepository = $currencyRepository;
        $this->productFields = iterator_to_array($productFields);
        $this->connection = $connection;
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
        if (!$this->getCurrencyList()) {
            $this->setCurrencyList();
        }

        if (!$this->getDefaultCurrency()) {
            $this->setDefaultCurrency();
        }

        $salesChannel = null;
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
            $feedService
                ->setCurrencyList($this->getCurrencyList())
                ->setDefaultCurrency($this->getDefaultCurrency())
                ->generate(new ConsoleOutput($output), $feedColumns)
            ;

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
        $baseFieldNames = (array)$this->container->getParameter('factfinder.export.columns.base');
        $productFieldNames = $this->getProductFieldNames($this->productFields);

        return array_values(array_unique(array_merge($baseFieldNames, $productFieldNames)));
    }

    private function getProductFieldNames(array $productFields): array
    {
        $fields = [];

        /** @var FieldInterface $productField */
        foreach ($productFields as $productField) {
            if ($productField instanceof Price) {
                foreach ($this->getCurrencyList() as $currency) {
                    $fields[] = $currency['factor'] == $this->getDefaultCurrency()->getFactor()
                        ? $productField->getName()
                        : $productField->getName() . '_' . $currency['iso_code'];
                }
            } else {
                $fields[] = $productField->getName();
            }
        }

        return $fields;
    }

    private function setCurrencyList(): self
    {
        $query = $this->connection->createQueryBuilder()
            ->select('iso_code, factor')
            ->from('currency')
            ->execute()
        ;

        $this->currencyList = $query->fetchAllAssociative();

        return $this;
    }

    private function setDefaultCurrency(): self
    {
        $this->defaultCurrency = $this->currencyRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('factor', 1)),
            new Context(new SystemSource())
        )
            ->getElements();

        $this->defaultCurrency = $this->defaultCurrency[array_key_first($this->defaultCurrency)];

        return $this;
    }

    public function getCurrencyList(): ?array
    {
        return $this->currencyList;
    }

    public function getDefaultCurrency(): ?CurrencyEntity
    {
        return $this->defaultCurrency;
    }
}
