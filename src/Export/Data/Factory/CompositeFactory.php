<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Factory;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class CompositeFactory implements FactoryInterface
{
    private \Traversable $exportEntityFactories;

    public function __construct(\Traversable $exportedEntityFactories)
    {
        $this->exportEntityFactories = $exportedEntityFactories;
    }

    public function handle(Entity $entity): bool
    {
        return false;
    }

    public function createEntities(Entity $entity, string $producedType): iterable
    {
        /** @var FactoryInterface $factory */
        foreach ($this->exportEntityFactories as $factory) {
            if ($factory->handle($entity)) {
                yield from $factory->createEntities($entity, $producedType);
            }
        }
    }
}
