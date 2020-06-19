<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\Stream\ConsoleOutput;
use Psr\Container\ContainerInterface;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\SalesChannelContext as SalesChannelContextAlias;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductExportCommand extends Command
{
    /** @var EntityRepositoryInterface */
    private $salesChannelRepository;

    /** @var SalesChannelContextFactory */
    private $salesChannelContextFactory;

    /** @var FeedFactory */
    private $feedFactory;

    /** @var ContainerInterface */
    private $container;

    public function __construct(
        EntityRepositoryInterface $salesChannelRepository,
        SalesChannelContextFactory $salesChannelContextFactory,
        FeedFactory $feedFactory,
        ContainerInterface $container
    ) {
        parent::__construct('factfinder:export:products');
        $this->salesChannelRepository     = $salesChannelRepository;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
        $this->feedFactory                = $feedFactory;
        $this->container                  = $container;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $feed = $this->feedFactory->create($this->getSalesChannelContext($this->getSalesChannel()));
        $feed->generate(new ConsoleOutput($output), $this->container->getParameter('factfinder.export.columns'));
    }

    private function getSalesChannel(): SalesChannelEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));
        $criteria->addAssociation('domains');
        return $this->salesChannelRepository->search($criteria, new Context(new SystemSource()))->first();
    }

    protected function getSalesChannelContext(SalesChannelEntity $salesChannel): SalesChannelContextAlias
    {
        return $this->salesChannelContextFactory->create('', $salesChannel->getId(), [
            SalesChannelContextService::LANGUAGE_ID => $salesChannel->getLanguageId(),
        ]);
    }
}
