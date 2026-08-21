<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\Communication\PushImportService;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Export\ChannelTypeNamingStrategy;
use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\BrandEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\CategoryEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\CmsPageEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity;
use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\FieldsProvider;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\Export\Stream\ConsoleOutput;
use Omikron\FactFinder\Shopware6\Export\Stream\CsvFile;
use Omikron\FactFinder\Shopware6\Upload\UploadService;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.MissingImport)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
#[AsCommand(name: 'factfinder:data:worker-export')]
class WorkerDataExportCommand extends Command
{
    public const SALES_CHANNEL_ARGUMENT          = 'sales_channel';
    public const SALES_CHANNEL_LANGUAGE_ARGUMENT = 'language';
    public const EXPORT_TYPE_ARGUMENT            = 'export_type';
    private const UPLOAD_FEED_OPTION             = 'upload';
    private const PUSH_IMPORT_OPTION             = 'import';
    private const BATCH_SIZE_OPTION              = 'batch-size';
    private const PRODUCTS_EXPORT_TYPE           = 'products';
    private const CMS_EXPORT_TYPE                = 'cms';
    private const CATEGORIES_EXPORT_TYPE         = 'category';
    private const BRANDS_EXPORT_TYPE             = 'brands';

    private $file;

    public function __construct(
        private readonly SalesChannelService $channelService,
        private readonly EntityRepository $channelRepository,
        private readonly FeedFactory $feedFactory,
        private readonly UploadService $uploadService,
        private readonly PushImportService $pushImportService,
        private readonly EntityRepository $languageRepository,
        private readonly CurrencyFieldsProvider $currencyFieldsProvider,
        private readonly FieldsProvider $fieldProviders,
        private readonly Communication $communication,
        private readonly array $productsColumnsBase,
        private readonly string $kernelProjectDir,
    ) {
        $this->file = tmpfile();
        parent::__construct();
    }

    public function getBaseTypeEntityMap(): array
    {
        return [
            self::PRODUCTS_EXPORT_TYPE   => ProductEntity::class,
            self::BRANDS_EXPORT_TYPE     => BrandEntity::class,
            self::CMS_EXPORT_TYPE        => CmsPageEntity::class,
            self::CATEGORIES_EXPORT_TYPE => CategoryEntity::class,
        ];
    }

    public function getTypeEntityMap(): array
    {
        return array_merge($this->getBaseTypeEntityMap());
    }

    public function configure(): void
    {
        $this->setName('factfinder:product-data:export');
        $this->setDescription('Allows to export feed for products in queue method');
        $this->addOption(self::UPLOAD_FEED_OPTION, 'u', InputOption::VALUE_NONE, 'Should upload after exporting');
        $this->addOption(self::PUSH_IMPORT_OPTION, 'i', InputOption::VALUE_NONE, 'Should import after uploading');
        $this->addOption(self::BATCH_SIZE_OPTION, 'b', InputOption::VALUE_OPTIONAL, 'Batch size for products export', 100);
        $this->addArgument(self::EXPORT_TYPE_ARGUMENT, InputArgument::OPTIONAL, sprintf('Set data export type(%s)', implode(', ', array_keys($this->getTypeEntityMap()))));
        $this->addArgument(self::SALES_CHANNEL_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel');
        $this->addArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel language');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $saveFile = false;

        if ($input->isInteractive()) {
            $helper = $this->getHelper('question');

            $exportTypeQuestion = $this->getChoiceQuestion(sprintf('Select data export type (default  - %s)', self::PRODUCTS_EXPORT_TYPE), array_keys($this->getTypeEntityMap()), 'Invalid option %s', 0);
            $exportType         = $helper->ask($input, $output, $exportTypeQuestion);

            $salesChannel      = $this->getSalesChannel($helper->ask($input, $output, new Question('ID of the sales channel (leave empty if no value): ')));
            $language          = $this->getLanguage($helper->ask($input, $output, new Question('ID of the sales channel language (leave empty if no value): ')));
            $batchSizeQuestion = new Question('Enter batch size for products export (default - 100): ', 100);
            $batchSize         = (int) $helper->ask($input, $output, $batchSizeQuestion);

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
            $batchSize        = (int) $input->getOption(self::BATCH_SIZE_OPTION);
        }

        if ($batchSize <= 0) {
            $batchSize = 100;
        }

        $context          = $this->channelService->getSalesChannelContext($salesChannel, $language->getId());
        $entityClass      = $this->getExportedEntityClass($exportType);
        $feedColumns      = $this->getFeedColumns($exportType, $entityClass);

        $needFile = $saveFile || $uploadFeed;

        if ($needFile) {
            fclose($this->file);
            $this->createFile($exportType, $context->getSalesChannelId());
        }

        $filePath = stream_get_meta_data($this->file)['uri'];

        if ($exportType === self::PRODUCTS_EXPORT_TYPE) {
            $phpBinaryFinder = new PhpExecutableFinder();
            $phpBinary       = $phpBinaryFinder->find() ?: 'php';

            $offset = 0;

            $output->writeln(sprintf('<info>Start generate data feed file by queue system (Batch size: %d):</info>', $batchSize));

            while (true) {
                $process = new Process([
                    $phpBinary,
                    'bin/console',
                    'factfinder:export:batch',
                    $salesChannel ? $salesChannel->getId() : '',
                    $language->getId(),
                    (string) $offset,
                    (string) $batchSize,
                    $filePath,
                    '--env=prod',
                ]);

                // 10 minutes for big batch size (with a lot of variants)
                $process->setTimeout(600);
                $process->run();

                if (!$process->isSuccessful()) {
                    $output->writeln("<error>Error during generate export (Offset: $offset).</error>");
                    $output->writeln($process->getErrorOutput());
                    return Command::FAILURE;
                }

                $rawOutput = trim($process->getOutput());
                preg_match('/\{.*\}/', $rawOutput, $matches);
                $jsonOutput = $matches[0] ?? '{}';

                $result         = json_decode($jsonOutput, true);
                $processedCount = $result['count'] ?? 0;

                if ($processedCount === 0) {
                    break;
                }

                $workerPeak   = $result['peak'] ?? 0;
                $masterPeak   = memory_get_peak_usage(true) / 1024 / 1024;

                $output->writeln(sprintf(
                    '[%s] <info>Processed batch</info> | Offset: <comment>%d</comment> | Worker Memory Peak: <comment>%.2f MB</comment> | Master Memory Peak: <comment>%.2f MB</comment>',
                    date('H:i:s'),
                    $offset,
                    $workerPeak,
                    $masterPeak
                ));

                $offset += $batchSize;
            }

            if (!$needFile) {
                $output->write(file_get_contents($filePath));
            }
        } else {
            // Old flow for CMS, CATEGORY and BRANDS
            $output->writeln(sprintf('<info>Start direct generate data feed for %s...</info>', $exportType));
            $feedService = $this->feedFactory->create($context, $entityClass);
            $out         = $needFile ? new CsvFile($this->file) : new ConsoleOutput($output);
            $feedService->generate($out, $feedColumns);
        }

        $output->writeln('<info>Generate export data feed file completed successfully.</info>');

        if ($uploadFeed) {
            $output->writeln('<info>Starting file upload to FACT-Finder...</info>');
            $this->uploadService->upload($this->file);
            $output->writeln('<info>Upload completed successfully.</info>');
        }

        if ($pushImport) {
            $output->writeln('<info>Triggering push import in FACT-Finder...</info>');
            $this->pushImportService->execute();
            $output->writeln('<info>Push import triggered successfully.</info>');
        }

        if (!$saveFile && $this->file) {
            $metaData = stream_get_meta_data($this->file);

            if (file_exists($metaData['uri'])) {
                unlink($metaData['uri']);
            }
        }

        $output->writeln('<info>All tasks finished!</info>');

        return Command::SUCCESS;
    }

    private function getSalesChannel(?string $id = null): ?SalesChannelEntity
    {
        return !is_null($id)
            ? $this->channelRepository->search(new Criteria([$id]), new Context(new SystemSource()))->first()
            : null;
    }

    private function getLanguage(?string $id = null): LanguageEntity
    {
        return $this->languageRepository->search(
            new Criteria([$id ?: Defaults::LANGUAGE_SYSTEM]),
            new Context(new SystemSource())
        )->first();
    }

    private function getFeedColumns(string $exportType, string $entityClass): array
    {
        $fields = $this->fieldProviders->getFields($entityClass);
        return array_values(
            array_unique(
                array_merge(
                    $this->productsColumnsBase,
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

    private function getExportedEntityClass(string $exportType): string
    {
        $entityTypeMap = $this->getTypeEntityMap();

        if (isset($entityTypeMap[$exportType])) {
            return $entityTypeMap[$exportType];
        }

        throw new \Exception('Unknown export type');
    }

    /**
     * @return false|resource
     *
     * @throws \Exception
     */
    private function createFile(string $exportType, string $salesChannelId)
    {
        $dir = $this->kernelProjectDir . '/var/factfinder';

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        if (!is_writable($dir)) {
            throw new \Exception('Directory ' . $dir . ' is not writable. Aborting');
        }

        $channelId  = $this->communication->getChannel($salesChannelId);
        $filename   = $dir . DIRECTORY_SEPARATOR . (new ChannelTypeNamingStrategy())->createFeedFileName($exportType, $channelId);
        $this->file = fopen($filename, 'w+');

        return $this->file;
    }
}
