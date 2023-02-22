<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

use Omikron\FactFinder\Shopware6\Domain\RedirectMapping;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ExtensionConfig extends BaseConfig
{
    private Request $request;

    public function __construct(SystemConfigService $systemConfig, ?RequestStack $requestStack = null)
    {
        $this->request = $requestStack ? $requestStack->getCurrentRequest() : new Request();
        parent::__construct($systemConfig);
    }

    public function getTrackingSettings(): array
    {
        return [
            'addToCart' => [
                'count' => (string) $this->config('trackingAddToCartCount') ?? 'count_as_one',
            ],
        ];
    }

    public function getRedirectMapping(): RedirectMapping
    {
        return new RedirectMapping(
            (string) $this->config(
                'redirectMapping',
                $this->request->attributes->get('sw-sales-channel-id')
            )
        );
    }
}
