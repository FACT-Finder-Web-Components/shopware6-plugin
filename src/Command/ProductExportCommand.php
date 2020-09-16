<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\Communication\PushImportService;
use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\Export\Stream\ConsoleOutput;
use Omikron\FactFinder\Shopware6\Export\Stream\CsvFile;
use Omikron\FactFinder\Shopware6\Upload\UploadService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Traversable;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
class ProductExportCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private const UPLOAD_FEED_OPTION = 'upload';
    private const PUSH_IMPORT_OPTION = 'import';

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

    public function __construct(
        SalesChannelService $channelService,
        FeedFactory $feedFactory,
        PushImportService $pushImportService,
        UploadService $uploadService,
        Traversable $productFields
    ) {
        parent::__construct('factfinder:export:products');
        $this->channelService    = $channelService;
        $this->feedFactory       = $feedFactory;
        $this->pushImportService = $pushImportService;
        $this->uploadService     = $uploadService;
        $this->productFields     = iterator_to_array($productFields);
    }

    protected function configure()
    {
        $this->setDescription('Export articles feed.');
        $this->addOption(self::UPLOAD_FEED_OPTION, 'u', InputOption::VALUE_NONE, 'Should upload after exporting');
        $this->addOption(self::PUSH_IMPORT_OPTION, 'i', InputOption::VALUE_NONE, 'Should import after uploading');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $feedService = $this->feedFactory->create($this->channelService->getSalesChannelContext());
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
        $base = (array) $this->container->getParameter('factfinder.export.columns.base');
        return array_values(array_unique(array_merge($base, array_map([$this, 'getFieldName'], $this->productFields))));
    }

    private function getFieldName(FieldInterface $field): string
    {
        return $field->getName();
    }
}
