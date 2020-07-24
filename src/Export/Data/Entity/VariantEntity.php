<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
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

    public function __construct(Product $product, array $parentData, PropertyFormatter $propertyFormatter)
    {
        $this->product           = $product;
        $this->parentData        = $parentData;
        $this->propertyFormatter = $propertyFormatter;
    }

    public function getId(): string
    {
        return $this->product->getId();
    }

    public function toArray(): array
    {
        $opts = '|' . implode('|', map($this->propertyFormatter, $this->product->getOptions()->getElements())) . '|';
        return ['ProductNumber' => $this->product->getProductNumber(), 'FilterAttributes' => $opts] + $this->parentData;
    }
}
