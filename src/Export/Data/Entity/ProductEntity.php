<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\Data\DataProviderInterface;
use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;

class ProductEntity implements ExportEntityInterface, DataProviderInterface
{
    /** @var SalesChannelProductEntity */
    private $product;

    /** @var FieldInterface[] */
    private $productFields;

    public function __construct(SalesChannelProductEntity $product, iterable $productFields)
    {
        $this->product       = $product;
        $this->productFields = iterator_to_array($productFields);
    }

    public function getId(): string
    {
        return $this->product->getId();
    }

    public function toArray(): array
    {
        return array_reduce($this->productFields, function (array $fields, FieldInterface $field) {
            return $fields + [$field->getName() => $field->getValue($this->product)];
        }, [
            'ProductNumber' => $this->product->getProductNumber(),
            'Name'          => $this->product->getName(),
        ]);
    }

    public function getEntities(): iterable
    {
        return [$this];
    }
}
