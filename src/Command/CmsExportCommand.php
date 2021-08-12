<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\Field\CMS\FieldInterface;
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
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
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
class CmsExportCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private const UPLOAD_FEED_OPTION              = 'upload';
    private const SALES_CHANNEL_ARGUMENT          = 'sales_channel';
    private const SALES_CHANNEL_LANGUAGE_ARGUMENT = 'language';

    private EntityRepositoryInterface $channelRepository;
    private EntityRepositoryInterface $languageRepository;
    private SalesChannelService $channelService;
    private FeedFactory $feedFactory;
    private UploadService $uploadService;
    /** @var FieldInterface[] */
    private array $cmsFields;

    public function __construct(
        EntityRepositoryInterface $channelRepository,
        EntityRepositoryInterface $languageRepository,
        SalesChannelService $channelService,
        FeedFactory $feedFactory,
        UploadService $uploadService,
        Traversable $cmsFields
    ) {
        parent::__construct();
        $this->channelRepository  = $channelRepository;
        $this->languageRepository = $languageRepository;
        $this->channelService     = $channelService;
        $this->feedFactory        = $feedFactory;
        $this->uploadService      = $uploadService;
        $this->cmsFields          = iterator_to_array($cmsFields);
    }

    public function configure()
    {
        $this->setName('factfinder:export:cms');
        $this->setDescription('Export articles feed.');
        $this->addOption(self::UPLOAD_FEED_OPTION, 'u', InputOption::VALUE_NONE, 'Should upload after exporting');
        $this->addArgument(self::SALES_CHANNEL_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel');
        $this->addArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel language');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $salesChannel     = $this->getSalesChannel($input);
        $selectedLanguage = $this->getLanguage($input);
        $feedService      = $this->feedFactory->create(
            $this->channelService->getSalesChannelContext($salesChannel, $selectedLanguage->getId()),
            FeedFactory::CMS_EXPORT_TYPE);

        $feedColumns      = $this->getFeedColumns();

        if (!$input->getOption(self::UPLOAD_FEED_OPTION)) {
            $feedService->generate(new ConsoleOutput($output), $feedColumns);
            return 0;
        }

        $fileHandle = tmpfile();
        $feedService->generate(new CsvFile($fileHandle), $feedColumns);
        $this->uploadService->upload($fileHandle);
        $output->writeln('Feed has been succesfully uploaded');

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

    private function getFeedColumns(): array
    {
        $base   = (array) $this->container->getParameter('factfinder.export.cms.columns.base');
        return array_values(array_unique(array_merge($base, array_map([$this, 'getFieldName'], $this->cmsFields))));
    }

    private function getFieldName(FieldInterface $field): string
    {
        return $field->getName();
    }
}
