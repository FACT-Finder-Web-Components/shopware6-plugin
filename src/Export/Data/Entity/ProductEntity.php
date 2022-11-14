<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionCollection;

class ProductEntity implements ExportEntityInterface
{
    private Product $product;

    /** @var FieldInterface[] */
    private iterable $productFields;

    public function __construct(Product $product, iterable $productFields)
    {
        $this->product       = $product;
        $this->productFields = $productFields;
    }

    public function getId(): string
    {
        return $this->product->getId();
    }

    public function getChildren(): ?ProductCollection
    {
        return $this->product->getChildren();
    }

    public function getProperties(): ?PropertyGroupOptionCollection
    {
        return $this->product->getProperties();
    }

    public function toArray(): array
    {
        return array_reduce($this->productFields, fn (array $fields, FieldInterface $field): array => $fields + [$field->getName() => $field->getValue($this->product)], [
            'ProductNumber' => $this->product->getProductNumber(),
            'Master'        => $this->product->getProductNumber(),
            'Name'          => (string) $this->product->getTranslation('name'),
        ]);
    }
}
