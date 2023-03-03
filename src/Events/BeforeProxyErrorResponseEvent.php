<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Events;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeProxyErrorResponseEvent extends Event
{
    private JsonResponse $response;

    public function __construct(JsonResponse $response)
    {
        $this->response = $response;
    }

    public function getResponse(): JsonResponse
    {
        return $this->response;
    }

    public function setResponse(JsonResponse $response): void
    {
        $this->response = $response;
    }
}
