<?php


namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;


use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Omikron\FactFinder\Shopware6\Export\Field\CMS\FieldInterface;

class CategoryEntity implements ExportEntityInterface
{
    private Category $category;

    /** @var FieldInterface[] */
    private iterable $cmsFields;

    public function __construct(Category $category, iterable $cmsFields)
    {
        $this->category = $category;
        $this->cmsFields = $cmsFields;
    }

    public function getId(): string
    {
        return $this->category->getId();
    }

    public function toArray(): array
    {
        return array_reduce($this->cmsFields, function ($fields, FieldInterface $field): array {
            $fields = !$fields ? [] : $fields;
            return $fields + [$field->getName() => $field->getValue($this->category)];
        });
    }

}
