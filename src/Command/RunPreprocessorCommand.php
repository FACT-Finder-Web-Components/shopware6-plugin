<?php
declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessor;
use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessorEntryPersister;
use Omikron\FactFinder\Shopware6\Export\ExportProducts;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Content\Product\ProductEntity;
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

class RunPreprocessorCommand extends Command
{
    public const SALES_CHANNEL_ARGUMENT = 'sales_channel';
    public const SALES_CHANNEL_LANGUAGE_ARGUMENT = 'language';

    private FeedPreprocessor $feedPreprocessor;
    private FeedPreprocessorEntryPersister $entryPersister;
    private ExportProducts $exportProducts;
    private SalesChannelService $channelService;
    private EntityRepositoryInterface $languageRepository;

    public function __construct(
        FeedPreprocessor               $feedPreprocessor,
        FeedPreprocessorEntryPersister $feedPreprocessorEntryPersister,
        SalesChannelService            $salesChannelService,
        ExportProducts                 $exportProducts,
        EntityRepositoryInterface      $languageRepository
    ) {
        parent::__construct();
        $this->feedPreprocessor   = $feedPreprocessor;
        $this->entryPersister     = $feedPreprocessorEntryPersister;
        $this->channelService     = $salesChannelService;
        $this->exportProducts     = $exportProducts;
        $this->languageRepository = $languageRepository;
    }

    protected function configure()
    {
        $this->setName('factfinder:data:pre-process');
        $this->setDescription('Run the Feed preprocessor');
        $this->addArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel language');
        $this->addArgument(self::SALES_CHANNEL_ARGUMENT, InputArgument::OPTIONAL, 'ID of the sales channel');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $salesChannel = $this->getSalesChannel($input->getArgument(self::SALES_CHANNEL_ARGUMENT));
        $language     = $this->getLanguage($input->getArgument(self::SALES_CHANNEL_LANGUAGE_ARGUMENT));
        $saleschannelContext = $this->channelService->getSalesChannelContext($salesChannel, $language->getId());
        $context = $saleschannelContext->getContext();
        $start = microtime(true);
        /** @var ProductEntity $product */
        foreach ($this->exportProducts->getByContext($saleschannelContext) as $product) {
            $this->entryPersister->deleteAllProductEntries($product->getProductNumber(),$context);
            $this->entryPersister->insertProductEntries($this->feedPreprocessor->createEntries($product, $context), $context);
        };
        $end = microtime(true);
        $execution_time = ($end - $start);
        $output->writeln($execution_time);
        return 0;
    }

    private function getLanguage(string $id = null): LanguageEntity
    {
        return $this->languageRepository->search(
            new Criteria([$id ?: Defaults::LANGUAGE_SYSTEM]),
            new Context(new SystemSource())
        )->first();
    }

    private function getSalesChannel(string $id = null): ?SalesChannelEntity
    {
        return !is_null($id)
            ? $this->channelRepository->search(new Criteria([$id]), new Context(new SystemSource()))->first()
            : null;
    }
}
