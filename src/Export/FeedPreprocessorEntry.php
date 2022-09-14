<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class FeedPreprocessorEntry extends Entity
{
    protected ?string $id;
    protected ?string $productNumber;
    protected ?string $parentProductNumber;
    protected ?string $variationKey;
    protected ?string $filterAttributes;
    protected ?string $customFields;
    protected ?string $languageId;
    protected array $additionalCache = [];

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getLanguageId(): ?string
    {
        return $this->languageId;
    }

    public function setLanguageId(?string $languageId): self
    {
        $this->languageId = $languageId;
        return $this;
    }

    public function getProductNumber(): ?string
    {
        return $this->productNumber;
    }

    public function setProductNumber(?string $productNumber): self
    {
        $this->productNumber = $productNumber;
        return $this;
    }

    public function getParentProductNumber(): ?string
    {
        return $this->parentProductNumber;
    }

    public function setParentProductNumber(?string $parentProductNumber): self
    {
        $this->parentProductNumber = $parentProductNumber;
        return $this;
    }

    public function getVariationKey(): ?string
    {
        return $this->variationKey;
    }

    public function setVariationKey(?string $variationKey): self
    {
        $this->variationKey = $variationKey;
        return $this;
    }

    public function getFilterAttributes(): ?string
    {
        return $this->filterAttributes;
    }

    public function setFilterAttributes(?string $filterAttributes): self
    {
        $this->filterAttributes = $filterAttributes;
        return $this;
    }

    public function getCustomFields(): ?string
    {
        return $this->customFields;
    }

    public function setCustomFields(?string $customFields): self
    {
        $this->customFields = $customFields;
        return $this;
    }

    public function getAdditionalCache(): ?array
    {
        return $this->additionalCache;
    }

    public function setAdditionalCache(?array $additionalCache): self
    {
        $this->additionalCache = $additionalCache;
        return $this;
    }
}
