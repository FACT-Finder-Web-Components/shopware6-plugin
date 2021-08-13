<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\Field\Brand\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\Export\Stream\ConsoleOutput;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
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

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BrandExportCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private const UPLOAD_FEED_OPTION              = 'upload';
    private const SALES_CHANNEL_ARGUMENT          = 'sales_channel';
    private const SALES_CHANNEL_LANGUAGE_ARGUMENT = 'language';

    private EntityRepositoryInterface $channelRepository;
    private EntityRepositoryInterface $languageRepository;
    private FeedFactory $feedFactory;
    private SalesChannelService $channelService;
    private array $fieldProviders;

    public function __construct(
        EntityRepositoryInterface $channelRepository,
        EntityRepositoryInterface $languageRepository,
        FeedFactory $feedFactory,
        SalesChannelService $channelService,
        array $fieldProviders
    ) {
        parent::__construct();
        $this->channelRepository  = $channelRepository;
        $this->languageRepository = $languageRepository;
        $this->feedFactory        = $feedFactory;
        $this->channelService     = $channelService;
        $this->fieldProviders     = $fieldProviders;
    }

    public function configure()
    {
        $this->setName('factfinder:export:brands');
        $this->setDescription('Export brands feed.');
        $this->addOption(self::UPLOAD_FEED_OPTION, 'u', InputOption::VALUE_NONE, 'Should upload after exporting');
        $this->addArgument(self::SALES_CHANNEL_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel');
        $this->addArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel language');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $salesChannel     = $this->getSalesChannel($input);
        $selectedLanguage = $this->getLanguage($input);
        $context          = $this->channelService->getSalesChannelContext($salesChannel, $selectedLanguage->getId());
        $feedService      = $this->feedFactory->create($context, ProductManufacturerEntity::class);
        $feedColumns      = $this->getFeedColumns();

        if (!$input->getOption(self::UPLOAD_FEED_OPTION)) {
            $feedService->generate(new ConsoleOutput($output), $feedColumns);
            return 0;
        }
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
        $base   = (array) $this->container->getParameter('factfinder.export.brands.columns.base');
        $fields = iterator_to_array($this->fieldProviders[ProductManufacturerEntity::class]);
        return array_values(array_unique(array_merge($base, array_map([$this, 'getFieldName'], $fields))));
    }

    private function getFieldName(FieldInterface $field): string
    {
        return $field->getName();
    }
}
