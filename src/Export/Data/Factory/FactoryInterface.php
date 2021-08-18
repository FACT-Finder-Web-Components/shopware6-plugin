<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Factory;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;

interface FactoryInterface
{
    public function handle(Entity $entity): bool;

    public function createEntities(Entity $entity, string $producedType): iterable;
}
