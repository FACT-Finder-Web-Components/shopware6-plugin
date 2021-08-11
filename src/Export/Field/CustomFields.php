<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomFields extends AbstractCustomField implements FieldInterface
{
    public function getName(): string
    {
        return 'CustomFields';
    }

    public function getValue($entity): string
    {
        return parent::getValue($entity);
    }

}
