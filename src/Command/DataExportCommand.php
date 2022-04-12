<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\Communication\PushImportService;
use Omikron\FactFinder\Shopware6\Export\ChannelTypeNamingStrategy;
use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\FieldsProvider;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\Export\Stream\ConsoleOutput;
use Omikron\FactFinder\Shopware6\Export\Stream\CsvFile;
use Omikron\FactFinder\Shopware6\Upload\UploadService;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.MissingImport)
 */
class DataExportCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public const SALES_CHANNEL_ARGUMENT          = 'sales_channel';
    public const SALES_CHANNEL_LANGUAGE_ARGUMENT = 'language';
    public const EXPORT_TYPE_ARGUMENT            = 'export_type';

    private const UPLOAD_FEED_OPTION              = 'upload';
    private const PUSH_IMPORT_OPTION              = 'import';
    private const PRODUCTS_EXPORT_TYPE            = 'products';
    private const CMS_EXPORT_TYPE                 = 'cms';
    private const BRANDS_EXPORT_TYPE              = 'brands';

    private SalesChannelService $channelService;
    private EntityRepositoryInterface $channelRepository;
    private FeedFactory $feedFactory;
    private UploadService $uploadService;
    private PushImportService $pushImportService;
    private EntityRepositoryInterface $languageRepository;
    private CurrencyFieldsProvider $currencyFieldsProvider;
    private FieldsProvider $fieldProviders;

    /** @@todo v4 remove this reference */
    private $file;

    public function __construct(
        SalesChannelService $channelService,
        EntityRepositoryInterface $channelRepository,
        FeedFactory $feedFactory,
        UploadService $uploadService,
        PushImportService $pushImportService,
        EntityRepositoryInterface $languageRepository,
        CurrencyFieldsProvider $currencyFieldsProvider,
        FieldsProvider $fieldProviders,
        ContainerInterface $container
    ) {
        $this->channelService         = $channelService;
        $this->channelRepository      = $channelRepository;
        $this->feedFactory            = $feedFactory;
        $this->uploadService          = $uploadService;
        $this->pushImportService      = $pushImportService;
        $this->languageRepository     = $languageRepository;
        $this->currencyFieldsProvider = $currencyFieldsProvider;
        $this->fieldProviders         = $fieldProviders;
        $this->file                   = tmpfile();
        $this->container              = $container;
        parent::__construct();
    }

    public function getBaseTypeEntityMap(): array
    {
        return [
            self::PRODUCTS_EXPORT_TYPE => SalesChannelProductEntity::class,
            self::BRANDS_EXPORT_TYPE   => ProductManufacturerEntity::class,
            self::CMS_EXPORT_TYPE      => CategoryEntity::class,
        ];
    }

    public function getTypeEntityMap(): array
    {
        return array_merge($this->getBaseTypeEntityMap(), $this->container->getParameter('factfinder.data_export.entity_type_map'));
    }

    public function configure(): void
    {
        $this->setName('factfinder:data:export');
        $this->setDescription('Allows to export feed for different data types');
        $this->addOption(self::UPLOAD_FEED_OPTION, 'u', InputOption::VALUE_NONE, 'Should upload after exporting');
        $this->addOption(self::PUSH_IMPORT_OPTION, 'i', InputOption::VALUE_NONE, 'Should import after uploading');
        $this->addArgument(self::EXPORT_TYPE_ARGUMENT, InputArgument::OPTIONAL, sprintf('Set data export type(%s)', implode(', ', array_keys($this->getTypeEntityMap()))));
        $this->addArgument(self::SALES_CHANNEL_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel');
        $this->addArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel language');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $saveFile = false;

        if ($input->isInteractive()) {
            $helper = $this->getHelper('question');

            $exportTypeQuestion = $this->getChoiceQuestion(sprintf('Select data export type (default  - %s)', self::PRODUCTS_EXPORT_TYPE), array_keys($this->getTypeEntityMap()), 'Invalid option %s', 0);
            $exportType         = $helper->ask($input, $output, $exportTypeQuestion);

            $salesChannel     = $this->getSalesChannel($helper->ask($input, $output, new Question('ID of the sales channel (leave empty if no value): ')));
            $language         = $this->getLanguage($helper->ask($input, $output, new Question('ID of the sales channel language (leave empty if no value): ')));

            $saveFileQuestion = $this->getChoiceQuestion('Save export to local file? (default  - no): ', ['no', 'yes'], 'Invalid option %s', 0);
            $saveFile         = (bool) array_flip($saveFileQuestion->getChoices())[$helper->ask($input, $output, $saveFileQuestion)];

            $uploadFeedQuestion = $this->getChoiceQuestion('Should upload after exporting? (default  - no): ', ['no', 'yes'], 'Invalid option %s', 0);
            $uploadFeed         = (bool) array_flip($uploadFeedQuestion->getChoices())[$helper->ask($input, $output, $uploadFeedQuestion)];

            $pushImportQuestion = $this->getChoiceQuestion('Should import after uploading? (default  - no): ', ['no', 'yes'], 'Invalid option %s', 0);
            $pushImport         = (bool) array_flip($pushImportQuestion->getChoices())[$helper->ask($input, $output, $pushImportQuestion)];
        } else {
            $salesChannel     = $this->getSalesChannel($input->getArgument(self::SALES_CHANNEL_ARGUMENT));
            $language         = $this->getLanguage($input->getArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT));
            $exportType       = $input->getArgument(self::EXPORT_TYPE_ARGUMENT) ?? self::PRODUCTS_EXPORT_TYPE;
            $uploadFeed       = $input->getOption(self::UPLOAD_FEED_OPTION);
            $pushImport       = $input->getOption(self::PUSH_IMPORT_OPTION);
        }

        $context          = $this->channelService->getSalesChannelContext($salesChannel, $language->getId());
        $entityFQN        = $this->getEntityFqnByType($exportType);
        $feedService      = $this->feedFactory->create($context, $entityFQN);
        $feedColumns      = $this->getFeedColumns($exportType, $entityFQN);

        $needFile = $saveFile || $uploadFeed;
        $output   = $needFile ? new CsvFile($this->createFile($exportType, $context->getSalesChannelId())) : new ConsoleOutput($output);
        $feedService->generate($output, $feedColumns);

        if ($uploadFeed) {
            $this->uploadService->upload($this->file);
        }

        if ($pushImport) {
            $this->pushImportService->execute();
        }

        if (!$saveFile && $this->file) {
            unlink(stream_get_meta_data($this->file)['uri']);
        }

        return 0;
    }

    private function getSalesChannel(string $id = null): ?SalesChannelEntity
    {
        return !is_null($id)
            ? $this->channelRepository->search(new Criteria([$id]), new Context(new SystemSource()))->first()
            : null;
    }

    private function getLanguage(string $id = null): LanguageEntity
    {
        return $this->languageRepository->search(
            new Criteria([$id ?: Defaults::LANGUAGE_SYSTEM]),
            new Context(new SystemSource())
        )->first();
    }

    private function getFeedColumns(string $exportType, string $entityFqn): array
    {
        $base   = (array) $this->container->getParameter(sprintf('factfinder.export.%s.columns.base', $exportType));
        $fields = $this->fieldProviders->getFields($entityFqn);
        return array_values(
            array_unique(
                array_merge(
                    $base,
                    array_map([$this, 'getFieldName'], $fields),
                    $exportType === self::PRODUCTS_EXPORT_TYPE ? $this->currencyFieldsProvider->getCurrencyFields() : [])));
    }

    private function getFieldName(FieldInterface $field): string
    {
        return $field->getName();
    }

    private function getChoiceQuestion(string $question, array $choices, string $errorMessage, $defaultValue = null): ChoiceQuestion
    {
        return (new ChoiceQuestion($question, $choices, $defaultValue))->setErrorMessage($errorMessage);
    }

    private function getEntityFqnByType(string $exportType): string
    {
        $entityTypeMap = $this->getTypeEntityMap();

        if (isset($entityTypeMap[$exportType])) {
            return $entityTypeMap[$exportType];
        }

        throw new \Exception('Unknown export type');
    }

    /**
     * @param string $exportType
     * @param string $salesChannelId
     *
     * @return false|resource
     *
     * @throws \Exception
     */
    private function createFile(string $exportType, string $salesChannelId)
    {
        $dir = $this->container->getParameter('kernel.project_dir') . '/var/factfinder';

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        if (!is_writable($dir)) {
            throw new \Exception('Directory ' . $dir . ' is not writable. Aborting');
        }

        /** @todo v4 refactor this */
        $channelId  = $this->container->get('Omikron\FactFinder\Shopware6\Config\Communication')->getChannel($salesChannelId);
        $filename   = $dir . DIRECTORY_SEPARATOR . (new ChannelTypeNamingStrategy())->createFeedFileName($exportType, $channelId);
        $this->file = fopen($filename, 'w+');

        return $this->file;
    }
}
