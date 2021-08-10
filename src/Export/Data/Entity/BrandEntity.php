<?php


namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;


use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity as Brand;
use Omikron\FactFinder\Shopware6\Export\Field\Brand\FieldInterface;

class BrandEntity implements ExportEntityInterface
{
    private Brand $brand;

    /** @var FieldInterface[]  */
    private iterable $brandFields;

    public function __construct(Brand $brand, iterable $brandFields)
    {
        $this->brand = $brand;
        $this->brandFields = $brandFields;
    }

    public function getId(): string
    {
        return $this->brand->getId();
    }

    public function toArray(): array
    {
        return array_reduce($this->brandFields, function (array $fields, FieldInterface $field): array {
            return $fields + [$field->getName() => $field->getValue($this->brand)];
        }, [
            'BrandName' => (string) $this->brand->getName(),
            'BrandLogo'        => (string) $this->brand->getMediaId(),
            'BrandURL'          => (string) $this->brand->getLink(),
        ]);
    }
}
