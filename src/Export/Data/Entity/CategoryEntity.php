<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Content\Category\CategoryEntity as ShopwareCategoryEntity;

class CategoryEntity implements ExportEntityInterface
{
    private ShopwareCategoryEntity $category;

    /** @var FieldInterface[] */
    private iterable $cmsFields;

    public function __construct(ShopwareCategoryEntity $category, iterable $cmsFields)
    {
        $this->category  = $category;
        $this->cmsFields = $cmsFields;
    }

    public function getId(): string
    {
        return $this->category->getId();
    }

    public function toArray(): array
    {
        return array_reduce($this->cmsFields, function (array $fields, FieldInterface $field): array {
            return $fields + [$field->getName() => $field->getValue($this->category)];
        }, [
            'Id'        => $this->category->getId() ?? '',
            'Name'      => $this->category->getName() ?? '',
            'Content'   => $this->category->getDescription() ?? '',
            'Keywords'  => $this->category->getKeywords() ?? '',
            'MetaTitle' => $this->category->getMetaTitle() ?? '',
        ]);
    }
}
