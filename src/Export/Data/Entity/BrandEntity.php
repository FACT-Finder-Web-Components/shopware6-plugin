<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity as Brand;

class BrandEntity implements ExportEntityInterface
{
    private Brand $brand;

    /** @var FieldInterface[] */
    private iterable $brandFields;

    public function __construct(Brand $brand, iterable $brandFields)
    {
        $this->brand       = $brand;
        $this->brandFields = $brandFields;
    }

    public function getId(): string
    {
        return $this->brand->getId();
    }

    public function toArray(): array
    {
        return array_reduce($this->brandFields, fn (array $fields, FieldInterface $field): array => $fields + [$field->getName() => $field->getValue($this->brand)], [
            'Name'              => (string) $this->brand->getName(),
            'Deeplink'          => (string) $this->brand->getLink(),
        ]);
    }
}
