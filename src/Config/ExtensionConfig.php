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

    public function __construct(
        SystemConfigService $systemConfig,
        ?RequestStack $requestStack = null
    ) {
        $this->setRequest($requestStack);
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

    private function setRequest(?RequestStack $requestStack = null): void
    {
        if ($requestStack === null) {
            $this->request = new Request();

            return;
        }

        $currentRequest = $requestStack->getCurrentRequest();

        if ($currentRequest === null) {
            $this->request = new Request();

            return;
        }

        $this->request = $currentRequest;
    }
}
