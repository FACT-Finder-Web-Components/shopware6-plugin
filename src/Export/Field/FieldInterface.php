<?php

declare(strict_types=1);

    namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;

/**
 * @api
 */
interface FieldInterface
{
    public function getName(): string;

    public function getValue(Entity $entity): string;

    public function getCompatibleEntityTypes(): array;
}
