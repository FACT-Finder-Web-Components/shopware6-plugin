<?php


namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;


use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity as Brand;


class BrandEntity implements ExportEntityInterface
{
    private Brand $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    public function getId(): string
    {
        return $this->brand->getId();
    }

    public function toArray(): array
    {
        return [
            'BrandName' => (string) $this->brand->getName(),
            'BrandLogo'        => (string) $this->brand->getMediaId(),
            'BrandURL'          => (string) $this->brand->getLink(),
        ];
    }
}
