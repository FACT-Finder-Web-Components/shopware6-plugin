<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class ProductEntity implements ExportEntityInterface, ProductEntityInterface
{
    private Product $product;
    private string $filterAttributes = '';
    private string $customFields = '';
    private array $additionalCache = [];

    /** @var FieldInterface[] */
    private iterable $productFields;

    /** @var FieldInterface[] */
    private iterable $cachedProductFields;

    public function __construct(
        Product $product,
        iterable $productFields,
        iterable $cachedProductFields
    ) {
        $this->product             = $product;
        $this->productFields       = $productFields;
        $this->cachedProductFields = $cachedProductFields;
    }

    public function getId(): string
    {
        return $this->product->getId();
    }

    public function getProductNumber(): string
    {
        return $this->product->getProductNumber();
    }

    public function getFilterAttributes(): string
    {
        return $this->filterAttributes;
    }

    public function getAdditionalCache(): array
    {
        return $this->additionalCache;
    }

    public function setFilterAttributes(string $filterAttributes): void
    {
        $this->filterAttributes = $filterAttributes;
    }

    public function setAdditionalCache(array $additionalCache): void
    {
        $this->additionalCache = $additionalCache;
    }

    public function getCustomFields(): string
    {
        return $this->customFields;
    }

    public function setCustomFields(string $customFields): void
    {
        $this->customFields = $customFields;
    }

    public function toArray(): array
    {
        $cachedProductFieldNames = array_map(fn(FieldInterface $field) => $field->getName(), iterator_to_array($this->cachedProductFields));
        $fields = array_filter($this->productFields, fn (FieldInterface $productField) => !in_array($productField->getName(), $cachedProductFieldNames));

        return array_reduce($fields, fn (array $fields, FieldInterface $field): array => $fields + [$field->getName() => $field->getValue($this->product)], [
            'ProductNumber'    => $this->product->getProductNumber(),
            'Master'           => $this->product->getProductNumber(),
            'Name'             => (string) $this->product->getTranslation('name'),
            'FilterAttributes' => $this->getFilterAttributes(),
            'CustomFields'     => $this->getCustomFields(),
        ]);
    }
}
