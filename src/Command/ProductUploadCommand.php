<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\Communication\PushImportService;
use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\Export\Stream\FtpFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ProductUploadCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var SalesChannelService */
    private $channelService;

    /** @var FeedFactory */
    private $feedFactory;

    /** @var FtpFactory */
    private $ftpFactory;

    /** @var PushImportService */
    private $pushImportService;

    public function __construct(
        SalesChannelService $channelService,
        FeedFactory $feedFactory,
        FtpFactory $ftpFactory,
        PushImportService $pushImportService
    ) {
        parent::__construct('factfinder:upload:products');
        $this->channelService    = $channelService;
        $this->feedFactory       = $feedFactory;
        $this->ftpFactory        = $ftpFactory;
        $this->pushImportService = $pushImportService;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $feed = $this->feedFactory->create($this->channelService->getSalesChannelContext());
        $feed->generate($this->ftpFactory->create(), $this->container->getParameter('factfinder.export.columns'));
        $this->pushImportService->execute();
    }
}
