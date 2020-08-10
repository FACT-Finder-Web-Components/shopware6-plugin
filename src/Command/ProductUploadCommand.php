<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Command;

use Omikron\FactFinder\Shopware6\Communication\PushImportService;
use Omikron\FactFinder\Shopware6\Export\Feed;
use Omikron\FactFinder\Shopware6\Export\FeedFactory;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\Export\Stream\StreamInterface;
use Omikron\FactFinder\Shopware6\Upload\UploadService;
use Psr\Container\ContainerInterface;
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

    /** @var UploadService */
    private $uploadService;

    /** @var PushImportService */
    private $pushImportService;

    public function __construct(
        SalesChannelService $channelService,
        FeedFactory $feedFactory,
        PushImportService $pushImportService,
        UploadService $uploadService,
        ContainerInterface $container
    ) {
        parent::__construct('factfinder:upload:products');
        $this->channelService    = $channelService;
        $this->feedFactory       = $feedFactory;
        $this->pushImportService = $pushImportService;
        $this->uploadService     = $uploadService;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->uploadService->upload($this->generate($this->feedFactory->create($this->channelService->getSalesChannelContext())));
        $output->writeln('Feed has been succesfully uploaded');
        $this->pushImportService->execute();
        $output->writeln('FACT-Finder import has been start');
    }

    private function generate(Feed $feed)
    {
        return function (StreamInterface $file) use ($feed): void {
            $feed->generate($file, $this->container->getParameter('factfinder.export.columns'));
        };
    }
}
