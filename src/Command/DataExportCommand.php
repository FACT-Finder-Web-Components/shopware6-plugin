<?php


namespace Omikron\FactFinder\Shopware6\Command;


use Omikron\FactFinder\Shopware6\Communication\PushImportService;
use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\FieldsProvider;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class DataExportCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait,
        DataExportTypeMapperTrait;

    private const UPLOAD_FEED_OPTION              = 'upload';
    private const PUSH_IMPORT_OPTION              = 'import';
    private const SALES_CHANNEL_ARGUMENT          = 'sales_channel';
    private const SALES_CHANNEL_LANGUAGE_ARGUMENT = 'language';
    private const EXPORT_TYPE = 'export_type';
    private const PRODUCTS_EXPORT_TYPE = 'products';
    private const CMS_EXPORT_TYPE = 'cms';
    private const BRANDS_EXPORT_TYPE = 'brands';

    private SalesChannelService $channelService;
    private EntityRepositoryInterface $channelRepository;
    private FeedFactory $feedFactory;
    private UploadService $uploadService;
    private PushImportService $pushImportService;
    private EntityRepositoryInterface $languageRepository;
    private CurrencyFieldsProvider $currencyFieldsProvider;
    private FieldsProvider $fieldProviders;

    public function __construct(
        SalesChannelService $channelService,
        EntityRepositoryInterface $channelRepository,
        FeedFactory $feedFactory,
        UploadService $uploadService,
        PushImportService $pushImportService,
        EntityRepositoryInterface $languageRepository,
        CurrencyFieldsProvider $currencyFieldsProvider,
        FieldsProvider $fieldProviders
    )
    {
        parent::__construct();
        $this->channelService = $channelService;
        $this->channelRepository = $channelRepository;
        $this->feedFactory = $feedFactory;
        $this->uploadService = $uploadService;
        $this->pushImportService = $pushImportService;
        $this->languageRepository = $languageRepository;
        $this->currencyFieldsProvider = $currencyFieldsProvider;
        $this->fieldProviders = $fieldProviders;
    }

    public function configure()
    {
        $this->setName('factfinder:data:export');
        $this->setDescription('Allows to export feed data for products, CMS and brands');
        $this->addArgument(self::EXPORT_TYPE, InputArgument::OPTIONAL, sprintf('Set data export type(%s, %s, %s', self::PRODUCTS_EXPORT_TYPE, self::CMS_EXPORT_TYPE, self::BRANDS_EXPORT_TYPE));
        $this->addArgument(self::SALES_CHANNEL_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel');
        $this->addArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel language');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->isInteractive()) {
            $helper = $this->getHelper('question');
            $dataExportTypeQuestion = new ChoiceQuestion(
                sprintf('Select data export type (default  - %s)', self::PRODUCTS_EXPORT_TYPE),
                [self::PRODUCTS_EXPORT_TYPE, self::CMS_EXPORT_TYPE, self::BRANDS_EXPORT_TYPE],
                0
            );
            $dataExportTypeQuestion->setErrorMessage('Invalid option %s');
            $entity = $this->getEntityFqnByType($helper->ask($input, $output, $dataExportTypeQuestion));

            $salesChannelQuestion = new Question('ID of the sales channel (leave empty if no value)');
            $salesChannel     = $this->getSalesChannel($helper->ask($input, $output, $salesChannelQuestion));

            $languageQuestion = new Question('ID of the sales channel language (leave empty if no value)');
            $language = $this->getLanguage($helper->ask($input, $output, $languageQuestion));

        } else {
            $salesChannel     = $this->getSalesChannel($input->getArgument(self::SALES_CHANNEL_ARGUMENT));
            $language = $this->getLanguage($input->getArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT));
            $entity = $this->getEntityFqnByType($input->getArgument(self::EXPORT_TYPE) ?? 'products');
        }

        $context          = $this->channelService->getSalesChannelContext($salesChannel, $language->getId());
        $feedService      = $this->feedFactory->create($context, $entity);
        $feedColumns      = $this->getFeedColumns();

        dump($entity, $salesChannel, $language);

        $output->writeln('no interactpion mode');

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

    private function getFeedColumns(): array
    {
        $base   = (array) $this->container->getParameter('factfinder.export.cms.columns.base');
        $fields = $this->fieldProviders->getFields(CategoryEntity::class);
        return array_values(array_unique(array_merge($base, array_map([$this, 'getFieldName'], $fields))));
    }

    private function getFieldName(FieldInterface $field): string
    {
        return $field->getName();
    }
}
