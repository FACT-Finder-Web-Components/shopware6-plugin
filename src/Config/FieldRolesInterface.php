<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

interface FieldRolesInterface
{
    public function getRoles(?string $salesChannelId): array;

    public function update(array $fieldRoles, ?string $salesChannelId): void;
}
