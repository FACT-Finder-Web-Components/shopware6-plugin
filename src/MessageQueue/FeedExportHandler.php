<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\MessageQueue;

use Omikron\FactFinder\Shopware6\Message\FeedExport;
use Shopware\Core\Framework\MessageQueue\Handler\AbstractMessageHandler;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class FeedExportHandler extends AbstractMessageHandler
{
    /** @var Application */
    private $application;

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
    public function handle($message): void
    {
        $input = new ArrayInput([
            'command'       => 'factfinder:export:products',
            '--upload'      => true,
            '--import'      => true,
            'sales_channel' => $message->getSalesChannelId() ?? '',
            'language'      => $message->getSalesChannelLanguageId() ?? '',
        ]);
        $output = new NullOutput();
        $this->application->run($input, $output);
    }

    public static function getHandledMessages(): iterable
    {
        return [FeedExport::class];
    }
}
