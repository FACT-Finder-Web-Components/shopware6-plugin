<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

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

    public function toArray(): array
    {
        return array_reduce($this->productFields, function (array $fields, FieldInterface $field): array {
            return $fields + [$field->getName() => $field->getValue($this->product)];
        }, [
            'ProductNumber' => (string) $this->product->getProductNumber(),
            'Master'        => (string) $this->product->getProductNumber(),
            'Name'          => (string) $this->product->getTranslation('name'),
        ]);
    }
}
