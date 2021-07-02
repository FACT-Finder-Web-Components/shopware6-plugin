<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\MessageHandler;

use Omikron\FactFinder\Shopware6\Message\FeedExport;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FeedExportHandler implements MessageHandlerInterface
{
    /** @var Application */
    private $application;

    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
        $this->application->setAutoExit(false);
    }

    public function __invoke(FeedExport $feedExport)
    {
        $input = new ArrayInput([
            'command'       => 'factfinder:export:products',
            '--upload'      => true,
            '--import'      => true,
            'sales_channel' => $feedExport->getSalesChannelId(),
            'language'      => $feedExport->getSalesChannelLanguageId(),
        ]);
        $output = new NullOutput();
        $this->application->run($input, $output);
    }
}
