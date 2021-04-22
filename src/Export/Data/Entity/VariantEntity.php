<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use function array_map as map;

class VariantEntity implements ExportEntityInterface
{
    /** @var Product */
    private $product;

    /** @var array */
    private $parentData;

    /** @var PropertyFormatter */
    private $propertyFormatter;

    /** @var FieldInterface[] */
    private $variantFields;

    public function __construct(
        Product $product,
        array $parentData,
        PropertyFormatter $propertyFormatter,
        iterable $variantFields
    ) {
        $this->product           = $product;
        $this->parentData        = $parentData;
        $this->propertyFormatter = $propertyFormatter;
        $this->variantFields     = $variantFields;
    }

    public function getId(): string
    {
        return $this->product->getId();
    }

    public function toArray(): array
    {
        $opts = '|' . implode('|', map($this->propertyFormatter, $this->product->getOptions()->getElements())) . '|';
        return array_reduce($this->variantFields, function (array $fields, FieldInterface $field) {
            return [$field->getName() => $field->getValue($this->product)] + $fields;
        }, ['ProductNumber' => $this->product->getProductNumber(), 'FilterAttributes' => $opts] + $this->parentData);
    }
}
