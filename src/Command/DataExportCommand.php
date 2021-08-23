<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\Communication\PushImportService;
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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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
    private ParameterBagInterface $parameterBag;

    public function __construct(
        SalesChannelService $channelService,
        EntityRepositoryInterface $channelRepository,
        FeedFactory $feedFactory,
        UploadService $uploadService,
        PushImportService $pushImportService,
        EntityRepositoryInterface $languageRepository,
        CurrencyFieldsProvider $currencyFieldsProvider,
        FieldsProvider $fieldProviders,
        ParameterBagInterface $parameterBag
    ) {
        parent::__construct();
        $this->channelService         = $channelService;
        $this->channelRepository      = $channelRepository;
        $this->feedFactory            = $feedFactory;
        $this->uploadService          = $uploadService;
        $this->pushImportService      = $pushImportService;
        $this->languageRepository     = $languageRepository;
        $this->currencyFieldsProvider = $currencyFieldsProvider;
        $this->fieldProviders         = $fieldProviders;
        $this->parameterBag           = $parameterBag;
    }

    public function getTypeEntityMap(): array
    {
        return [
            self::PRODUCTS_EXPORT_TYPE => SalesChannelProductEntity::class,
            self::BRANDS_EXPORT_TYPE   => ProductManufacturerEntity::class,
            self::CMS_EXPORT_TYPE      => CategoryEntity::class,
        ];
    }

    public function configure()
    {
        $this->setName('factfinder:data:export');
        $this->setDescription('Allows to export feed data for products, CMS and brands');
        $this->addOption(self::UPLOAD_FEED_OPTION, 'u', InputOption::VALUE_NONE, 'Should upload after exporting');
        $this->addOption(self::PUSH_IMPORT_OPTION, 'i', InputOption::VALUE_NONE, 'Should import after uploading');
        $this->addArgument(self::EXPORT_TYPE_ARGUMENT, InputArgument::OPTIONAL, sprintf('Set data export type(%s, %s, %s', self::PRODUCTS_EXPORT_TYPE, self::CMS_EXPORT_TYPE, self::BRANDS_EXPORT_TYPE));
        $this->addArgument(self::SALES_CHANNEL_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel');
        $this->addArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel language');
    }


    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->isInteractive()) {
            $helper = $this->getHelper('question');

            $exportTypeQuestion = $this->getChoiceQuestion(sprintf('Select data export type (default  - %s)', self::PRODUCTS_EXPORT_TYPE), [self::PRODUCTS_EXPORT_TYPE, self::CMS_EXPORT_TYPE, self::BRANDS_EXPORT_TYPE], 'Invalid option %s', 0);
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

        if (isset($saveFile) && $saveFile) {
            $dir = $this->parameterBag->get('kernel.project_dir') . DIRECTORY_SEPARATOR . 'var/factfinder';

            if (!is_dir($dir)) {
                mkdir($dir);
            }

            $filename = $dir . DIRECTORY_SEPARATOR . 'test.csv';
            $file     = fopen($filename, 'rw+');
            $feedService->generate(new CsvFile($file), $feedColumns);
        }

        if (!$uploadFeed) {
            $feedService->generate(new ConsoleOutput($output), $feedColumns);
            return 0;
        }

        if ($pushImport) {
            $this->pushImportService->execute();
            $output->writeln('FACT-Finder import has been start');
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
        return array_values(array_unique(array_merge($base, array_map([$this, 'getFieldName'], $fields))));
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
}
