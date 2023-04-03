<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

use Symfony\Component\Cache\Adapter\AdapterInterface;

class CachedFieldRoles implements FieldRolesInterface
{
    private FieldRolesInterface $decorated;
    private AdapterInterface $cache;

    public function __construct(FieldRolesInterface $decorated, AdapterInterface $cache)
    {
        $this->decorated = $decorated;
        $this->cache = $cache;
    }

    public function getRoles(?string $salesChannelId): array
    {
        $cacheKey = $this->getCacheKey($salesChannelId);
        $item = $this->cache->getItem($cacheKey);

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
        $this->decorated->update($fieldRoles, $salesChannelId);

        $cacheKey = $this->getCacheKey($salesChannelId);
        if ($this->cache->hasItem($cacheKey)) {
            $this->cache->deleteItem($cacheKey);
        }
    }

    private function getCacheKey(?string $salesChannelId): string
    {
        return 'factfinder-field-roles-' . ($salesChannelId ?? '');
    }
}
