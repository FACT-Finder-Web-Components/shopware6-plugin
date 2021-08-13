<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Factory;

use InvalidArgumentException;

trait FactoryConfigAware
{
    private array $factoryConfig;

    public function setFactoryConfig(array $factoryConfig): void
    {
        $this->factoryConfig = $factoryConfig;
    }

    public function getCreatedType(string $type): string
    {
        if (!isset($this->factoryConfig[$type])) {
            throw new InvalidArgumentException('There is no ExportEntity Factory for given Entity type');
        }
        return $this->factoryConfig[$type];
    }
}
