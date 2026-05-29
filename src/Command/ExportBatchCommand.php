<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\Export\CurrencyFieldsProvider;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity;
use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\FieldsProvider;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\Export\Stream\CsvFile;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'factfinder:export:batch', description: 'Internal worker command for exporting a batch of products', hidden: true)]
class ExportBatchCommand extends Command
{
    private const PRODUCTS_EXPORT_TYPE = 'products';

    public function __construct(
        private readonly SalesChannelService $channelService,
        private readonly FeedFactory $feedFactory,
        private readonly FieldsProvider $fieldProviders,
        private readonly array $productsColumnsBase,
        private readonly CurrencyFieldsProvider $currencyFieldsProvider,
        private readonly EntityRepository $salesChannelRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('sales_channel', InputArgument::REQUIRED, 'ID of the sales channel');
        $this->addArgument('language', InputArgument::REQUIRED, 'ID of the language');
        $this->addArgument('offset', InputArgument::REQUIRED, 'Offset');
        $this->addArgument('limit', InputArgument::REQUIRED, 'Limit');
        $this->addArgument('file_path', InputArgument::REQUIRED, 'Path to output CSV');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $offset         = (int) $input->getArgument('offset');
        $limit          = (int) $input->getArgument('limit');
        $filePath       = $input->getArgument('file_path');
        $salesChannelId = $input->getArgument('sales_channel');
        $salesChannel   = null;

        if (!empty($salesChannelId)) {
            $salesChannel = $this->salesChannelRepository->search(
                new Criteria([$salesChannelId]),
                new Context(new SystemSource())
            )->first();
        }

        $context = $this->channelService->getSalesChannelContext(
            $salesChannel,
            $input->getArgument('language')
        );

        $entityClass = ProductEntity::class;
        $feedService = $this->feedFactory->create($context, $entityClass);

        $fileResource = fopen($filePath, 'a');
        $out          = new CsvFile($fileResource);

        $feedColumns = $this->getFeedColumns('products', ProductEntity::class);
        $processedCount = $feedService->generateBatch($out, $feedColumns, $offset, $limit, $offset === 0);

        fclose($fileResource);

        $memoryUsageMB = memory_get_usage(true) / 1024 / 1024;
        $peakMemoryMB  = memory_get_peak_usage(true) / 1024 / 1024;

        $result = [
            'count'  => $processedCount,
            'memory' => round($memoryUsageMB, 2),
            'peak'   => round($peakMemoryMB, 2),
        ];

        $output->write(json_encode($result));

        return Command::SUCCESS;
    }

    private function getFeedColumns(string $exportType, string $entityClass): array
    {
        $fields = $this->fieldProviders->getFields($entityClass);
        return array_values(
            array_unique(
                array_merge(
                    $this->productsColumnsBase,
                    array_map([$this, 'getFieldName'], $fields),
                    $exportType === self::PRODUCTS_EXPORT_TYPE ? $this->currencyFieldsProvider->getCurrencyFields() : []
                )
            )
        );
    }

    private function getFieldName(FieldInterface $field): string
    {
        return $field->getName();
    }
}
