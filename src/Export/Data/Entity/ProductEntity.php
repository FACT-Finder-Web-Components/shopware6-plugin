<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use ArrayIterator;
use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity as Product;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionCollection;
use Traversable;
use function Omikron\FactFinder\Shopware6\Internal\Utils\safeGetByName;

class ProductEntity implements ExportEntityInterface, ProductEntityInterface
{
    private Product $product;
    private ?Product $parent         = null;
    private string $filterAttributes = '';
    private string $customFields     = '';
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
        $this->additionalCache     = new ArrayIterator();
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

    public function setParent(?Product $parent): void
    {
        $this->parent = $parent;
    }

    public function toArray(): array
    {
        $cachedProductFieldNames = array_map(fn (FieldInterface $field) => $field->getName(), iterator_to_array($this->cachedProductFields));
        $fields                  = array_filter($this->productFields, fn (FieldInterface $productField) => !in_array($productField->getName(), $cachedProductFieldNames));
        $isVariant               = $this->product->getId() !== $this->product->getParentId() && isset($this->parent);
        $defaultFields           = [
            'ProductNumber'    => $this->product->getProductNumber(),
            'Master'           => $isVariant ? $this->parent->getProductNumber() : $this->product->getProductNumber(),
            'Name'             => (string) $this->product->getTranslation('name'),
            'FilterAttributes' => $this->getFilterAttributes(),
            'CustomFields'     => $this->getCustomFields(),
        ];

        return array_reduce(
            $fields,
            fn (array $fields, FieldInterface $field): array => [$field->getName() => ($this->getAdditionalCache($field->getName()) ?? $field->getValue($isVariant ? $this->parent : $this->product))] + $fields,
            $defaultFields
        );
    }
}
