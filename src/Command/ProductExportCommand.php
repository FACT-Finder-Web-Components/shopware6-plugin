<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\Export\Stream\ConsoleOutput;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductExportCommand extends Command
{
    /** @var SalesChannelService */
    private $channelService;

    /** @var FeedFactory */
    private $feedFactory;

    /** @var ContainerInterface */
    private $container;

    public function __construct(
        SalesChannelService $channelService,
        FeedFactory $feedFactory,
        ContainerInterface $container
    ) {
        parent::__construct('factfinder:export:products');
        $this->channelService       = $channelService;
        $this->feedFactory          = $feedFactory;
        $this->container            = $container;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $feed = $this->feedFactory->create($this->channelService->getSalesChannelContext());
        $feed->generate(new ConsoleOutput($output), $this->container->getParameter('factfinder.export.columns'));
    }
}
