<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use function Omikron\FactFinder\Shopware6\Internal\Utils\safeGetByName;
use Traversable;

class ProductEntity implements ExportEntityInterface, ProductEntityInterface
{
    private Product $product;
    private string $filterAttributes = '';
    private string $customFields = '';
    private Traversable $additionalCache;

    /** @var FieldInterface[] */
    private iterable $productFields;

    /** @var FieldInterface[] */
    private iterable $cachedProductFields;

    public function __construct(
        Product $product,
        Traversable $productFields,
        Traversable $cachedProductFields
    ) {
        $this->product             = $product;
        $this->productFields       = iterator_to_array($productFields);
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

    public function getAdditionalCache(string $key): ?string
    {
        return safeGetByName(iterator_to_array($this->additionalCache), $key);
    }

    public function setFilterAttributes(string $filterAttributes): void
    {
        $this->filterAttributes = $filterAttributes;
    }

    public function setAdditionalCache(Traversable $additionalCache): void
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
        $defaultFields = [
            'ProductNumber'    => $this->product->getProductNumber(),
            'Master'           => $this->product->getProductNumber(),
            'Name'             => (string) $this->product->getTranslation('name'),
            'FilterAttributes' => $this->getFilterAttributes(),
            'CustomFields'     => $this->getCustomFields(),
        ];

        return array_reduce(
            $fields,
            fn (array $fields, FieldInterface $field): array => $fields
                + [$field->getName() => ($this->getAdditionalCache($field->getName()) ?? $field->getValue($this->product))],
            $defaultFields
        );
    }
}
