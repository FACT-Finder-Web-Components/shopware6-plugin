<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Cookie;

use Omikron\FactFinder\Shopware6\Subscriber\BeforeSendResponseEventSubscriber;
use Shopware\Storefront\Framework\Cookie\CookieProviderInterface;

class CookieProvider implements CookieProviderInterface
{
    private const HAS_JUST_LOGGED_IN = [
        'snippet_name' => BeforeSendResponseEventSubscriber::HAS_JUST_LOGGED_IN,
        'snippet_description' => 'Cookie required for proper working of user login tracking event',
        'cookie' => BeforeSendResponseEventSubscriber::HAS_JUST_LOGGED_IN,
    ];

    private const HAS_JUST_LOGGED_OUT = [
        'snippet_name' => BeforeSendResponseEventSubscriber::HAS_JUST_LOGGED_OUT,
        'snippet_description' => 'Cookie required for proper working of user login tracking event',
        'cookie' => BeforeSendResponseEventSubscriber::HAS_JUST_LOGGED_OUT,
    ];

    private const USER_ID = [
        'snippet_name' => BeforeSendResponseEventSubscriber::USER_ID,
        'snippet_description' => 'Cookie required for proper working of user login tracking event',
        'cookie' => BeforeSendResponseEventSubscriber::USER_ID,
    ];

    private CookieProviderInterface $originalService;

    public function __construct(CookieProviderInterface $service)
    {
        $this->originalService = $service;
    }

    public function getCookieGroups(): array
    {
        return array_merge(
            $this->originalService->getCookieGroups(),
            [
                self::HAS_JUST_LOGGED_IN,
                self::HAS_JUST_LOGGED_OUT,
                self::USER_ID,
            ]
        );
    }
}
