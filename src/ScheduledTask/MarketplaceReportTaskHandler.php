<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\ScheduledTask;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: MarketplaceReportTask::class)]
class MarketplaceReportTaskHandler extends ScheduledTaskHandler
{
    public const API_IDENTIFIER = 'd7ae3439-1a5c-45bd-b5ca-172b42511c7f';
    private Client $client;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        private EntityRepository $customerRepository,
        private readonly string $shopwareVersion,
        private readonly ?string $instanceId
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->client = new Client();
    }

    public static function getHandledMessages(): iterable
    {
        return [MarketplaceReportTask::class];
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function run(): void
    {
        $customers = $this->customerRepository->search(new Criteria(), Context::createDefaultContext());
        $now       = new \DateTime();
        $data      = [
            'identifier'      => self::API_IDENTIFIER,
            'reportDate'      => $now->format(\DateTimeInterface::ATOM),
            'instanceId'      => $this->instanceId,
            'shopwareVersion' => $this->shopwareVersion,
            'reportDataKeys'  => [
                'numberOfSubscriptions' => 0,
                'numberOfCustomers'     => $customers->getTotal(),
            ],
        ];
        $request = new Request(
            'POST',
            'https://api.shopware.com/shopwarepartners/reports/technology',
            [],
            json_encode($data)
        );
        $this->client->send($request);
    }
}
