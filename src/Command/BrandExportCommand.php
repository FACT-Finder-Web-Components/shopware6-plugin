<?php


namespace Omikron\FactFinder\Shopware6\Command;


use Shopware\Core\System\Language\LanguageEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Defaults;

class BrandExportCommand extends Command
{
    private const UPLOAD_FEED_OPTION              = 'upload';
    private const SALES_CHANNEL_ARGUMENT          = 'sales_channel';
    private const SALES_CHANNEL_LANGUAGE_ARGUMENT = 'language';

    private EntityRepositoryInterface $channelRepository;
    private EntityRepositoryInterface $languageRepository;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
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
