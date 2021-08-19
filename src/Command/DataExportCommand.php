<?php


namespace Omikron\FactFinder\Shopware6\Command;


use Omikron\FactFinder\Shopware6\Communication\PushImportService;
use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\FieldsProvider;
use Omikron\FactFinder\Shopware6\Upload\UploadService;
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
    use ContainerAwareTrait;

    private const UPLOAD_FEED_OPTION              = 'upload';
    private const PUSH_IMPORT_OPTION              = 'import';
    private const SALES_CHANNEL_ARGUMENT          = 'sales_channel';
    private const SALES_CHANNEL_LANGUAGE_ARGUMENT = 'language';
    private const EXPORT_TYPE = 'export_type';
    private const PRODUCTS_EXPORT_TYPE = 'products';
    private const CMS_EXPORT_TYPE = 'cms';
    private const BRANDS_EXPORT_TYPE = 'brands';

    private EntityRepositoryInterface $channelRepository;
    private FeedFactory $feedFactory;
    private UploadService $uploadService;
    private PushImportService $pushImportService;
    private EntityRepositoryInterface $languageRepository;
    private CurrencyFieldsProvider $currencyFieldsProvider;
    private FieldsProvider $fieldProviders;

    public function __construct(
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
            $dataExportType = $helper->ask($input, $output, $dataExportTypeQuestion);

            $salesChannelQuestion = new Question('ID of the sales channel (leave empty if no value)');
            $salesChannel = $helper->ask($input, $output, $salesChannelQuestion);

            $languageQuestion = new Question('ID of the sales channel language (leave empty if no value)');
            $language = $helper->ask($input, $output, $languageQuestion);

        } else {
            $salesChannel     = $this->getSalesChannel($input);
            $language = $this->getLanguage($input);
            $dataExportType = $input->getArgument(self::EXPORT_TYPE) ?? 'products';
        }

        dump($dataExportType, $salesChannel, $language);

        $output->writeln('no interactpion mode');

        return 0;
    }

    private function getSalesChannel(InputInterface $input): ?SalesChannelEntity
    {
        return !empty($input->getArgument(self::SALES_CHANNEL_ARGUMENT))
            ? $this->channelRepository->search(new Criteria([$input->getArgument(self::SALES_CHANNEL_ARGUMENT)]), new Context(new SystemSource()))->first()
            : null;
    }

    private function getLanguage(InputInterface $input): LanguageEntity
    {
        return $this->languageRepository->search(
            new Criteria([$input->getArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT) ?: Defaults::LANGUAGE_SYSTEM]),
            new Context(new SystemSource())
        )->first();
    }
}
