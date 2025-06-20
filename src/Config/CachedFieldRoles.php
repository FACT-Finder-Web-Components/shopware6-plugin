<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

use Symfony\Component\Cache\Adapter\AdapterInterface;

class CachedFieldRoles implements FieldRolesInterface
{
    private array $defaultFieldRoles;

    public function __construct(
        private readonly FieldRolesInterface $decorated,
        private readonly AdapterInterface $cache,
        private readonly string $kernelEnv,
        array $fieldRoles,
    ) {
        $this->defaultFieldRoles = $fieldRoles;
    }

    public function getRoles(?string $salesChannelId): array
    {
        if ($this->isDevEnv()) {
            return $this->defaultFieldRoles;
        }

        $salesChannelId = $salesChannelId ?? '';
        $cacheKey       = $this->getCacheKey($salesChannelId);
        $item           = $this->cache->getItem($cacheKey);

        if ($item->isHit()) {
            return $item->get();
        }

        $fieldRoles = $this->decorated->getRoles($salesChannelId);
        $item->set($fieldRoles);
        $this->cache->save($item);

        return $fieldRoles;
    }

    public function update(array $fieldRoles, ?string $salesChannelId): void
    {
        $salesChannelId = $salesChannelId ?? '';
        $this->decorated->update($fieldRoles, $salesChannelId);
        $cacheKey = $this->getCacheKey($salesChannelId);

        if ($this->cache->hasItem($cacheKey)) {
            $this->cache->deleteItem($cacheKey);
        }
    }

    private function getCacheKey(string $salesChannelId): string
    {
        return sprintf('factfinder-field-roles-%s', $salesChannelId);
    }

    private function isDevEnv(): bool
    {
        return $this->kernelEnv === 'dev';
    }
}
