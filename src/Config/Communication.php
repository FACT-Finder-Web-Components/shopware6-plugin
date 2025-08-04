<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

use Omikron\FactFinder\Communication\Version;

class Communication extends BaseConfig
{
    public function getServerUrl(): string
    {
        return trim((string) $this->config('serverUrl'));
    }

    public function getChannel(?string $salesChannelId = null): string
    {
        return (string) $this->config('channel', $salesChannelId);
    }

    public function getCredentials(): array
    {
        return [
            (string) $this->config('username'),
            (string) $this->config('password'),
        ];
    }

    public function getApiKey(): string
    {
        return (string) $this->config('apiKey');
    }

    public function isSsrActive(): bool
    {
        return (bool) $this->config('useSsr');
    }

    public function getFieldRoles(?string $salesChannelId): array
    {
        return (array) $this->config('fieldRoles', $salesChannelId);
    }

    public function getVersion(): string
    {
        return Version::NG;
    }

    public function getApiVersion(): string
    {
        return 'v5';
    }

    public function isProxyEnabled(): bool
    {
        return (bool) $this->config('useProxy');
    }

    public function isSsrPdpEnabled(): bool
    {
        return (bool) $this->config('useSsrPdp');
    }

    public function isCartBtnEnabled(): bool
    {
        return (bool) $this->config('addCartBtn');
    }

    public function getFactFinderFeatures(): array
    {
        return [
            'useAdvisorCampaigns'        => (bool) $this->config('useAdvisorCampaigns'),
            'useRedirectCampaigns'       => (bool) $this->config('useRedirectCampaigns'),
            'useFeedbackCampaigns'       => (bool) $this->config('useFeedbackCampaigns'),
            'useProductCampaigns'        => (bool) $this->config('useProductCampaigns'),
            'useShoppingCartCampaigns'   => (bool) $this->config('useShoppingCartCampaigns'),
            'useRecommendations'         => (bool) $this->config('useRecommendations'),
            'useSimilarProducts'         => (bool) $this->config('useSimilarProducts'),
            'usePushedProductsCampaigns' => (bool) $this->config('usePushedProductsCampaigns'),
            'usePopularSearches'         => (bool) $this->config('usePopularSearches'),
        ];
    }
}
