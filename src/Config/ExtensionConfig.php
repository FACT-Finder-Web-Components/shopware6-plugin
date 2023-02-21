<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

use Omikron\FactFinder\Shopware6\Domain\RedirectMapping;

class ExtensionConfig extends BaseConfig
{
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
        return new RedirectMapping((string) $this->config('redirectMapping'));
    }
}
