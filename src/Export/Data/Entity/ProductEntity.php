<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\Data\DataProviderInterface;
use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;

class ProductEntity implements ExportEntityInterface, DataProviderInterface
{
    /** @var SalesChannelProductEntity */
    private $product;

    public function __construct(SalesChannelProductEntity $product)
    {
        $this->product = $product;
    }

    public function getId(): string
    {
        return $this->product->getId();
    }

    public function toArray(): array
    {
        return [
            'ProductNumber' => $this->product->getProductNumber(),
            'Name'          => $this->product->getName(),
        ];
    }

    public function getEntities(): iterable
    {
        return [$this];
    }
}
