<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\MessageQueue;

use Omikron\FactFinder\Shopware6\Command\RunPreprocessorCommand;
use Omikron\FactFinder\Shopware6\Message\RefreshExportCache;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class RefreshExportCacheHandler implements MessageSubscriberInterface
{
    private Application $application;

    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
        $this->application->setAutoExit(false);
    }

    /**
     * @param RefreshExportCache $message
     *
     * @throws \Exception
     */
    public function __invoke(RefreshExportCache $message): void
    {
        $input = new ArrayInput([
            'command'                                                    => 'factfinder:data:pre-process',
            RunPreprocessorCommand::SALES_CHANNEL_ARGUMENT               => $message->getSalesChannelId(),
            RunPreprocessorCommand::SALES_CHANNEL_LANGUAGE_ARGUMENT      => $message->getSalesChannelLanguageId(),
        ]);
        $input->setInteractive(false);
        $output = new NullOutput();
        $this->application->run($input, $output);
    }

    public static function getHandledMessages(): iterable
    {
        return [RefreshExportCache::class];
    }
}
