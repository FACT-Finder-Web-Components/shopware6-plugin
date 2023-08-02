<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\MessageQueue;

use Omikron\FactFinder\Shopware6\Command\DataExportCommand;
use Omikron\FactFinder\Shopware6\Message\FeedExport;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class FeedExportHandler implements MessageSubscriberInterface
{
    private Application $application;

    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
        $this->application->setAutoExit(false);
    }

    /**
     * @param FeedExport $message
     *
     * @throws \Exception
     */
    public function __invoke(FeedExport $message): void
    {
        $input = new ArrayInput([
            'command'                                               => 'factfinder:data:export',
            '--upload'                                              => true,
            '--import'                                              => true,
            DataExportCommand::EXPORT_TYPE_ARGUMENT                 => $message->getExportTypeValue() ?? '',
            DataExportCommand::SALES_CHANNEL_ARGUMENT               => $message->getSalesChannelId(),
            DataExportCommand::SALES_CHANNEL_LANGUAGE_ARGUMENT      => $message->getSalesChannelLanguageId(),
        ]);
        $input->setInteractive(false);
        $output = new NullOutput();
        $this->application->run($input, $output);
    }

    public static function getHandledMessages(): iterable
    {
        return [FeedExport::class];
    }
}
