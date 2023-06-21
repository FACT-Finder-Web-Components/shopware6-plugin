<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Cookie;

use Omikron\FactFinder\Shopware6\Subscriber\BeforeSendResponseEventSubscriber;
use Shopware\Storefront\Framework\Cookie\CookieProviderInterface;

class CookieProvider implements CookieProviderInterface
{
    private const FF_COOKIE_GROUP = [
        'snippet_name' => 'ff.cookie.groupName',
        'snippet_description' => 'ff.cookie.groupDescription',
        'entries' => [
            [
                'snippet_name'        => 'ff.cookie.hasJustLogIn',
                'cookie'              => BeforeSendResponseEventSubscriber::HAS_JUST_LOGGED_IN,
            ],
            [
                'snippet_name'        =>'ff.cookie.hasJustLogOut',
                'cookie'              => BeforeSendResponseEventSubscriber::HAS_JUST_LOGGED_OUT,
            ],
            [
                'snippet_name'        => 'ff.cookie.userId',
                'cookie'              => BeforeSendResponseEventSubscriber::USER_ID,
            ],
        ],
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
            [self::FF_COOKIE_GROUP]
        );
    }
}
