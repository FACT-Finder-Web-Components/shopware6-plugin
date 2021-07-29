<?php


namespace Omikron\FactFinder\Shopware6\Export\Data\Entity;


use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;


class CategoryEntity implements ExportEntityInterface
{
    private Category $category;

    /** @var FieldInterface[] */
    private iterable $categoryFields;

    public function __construct(Category $category, $categoryFields)
    {
        $this->category = $category;
        $this->categoryFields = $categoryFields;
    }

    public function getId(): string
    {
        return $this->category->getId();
    }

    public function toArray(): array
    {
        return array_reduce($this->categoryFields, function (array $fields, FieldInterface $field): array {
            return $fields + [$field->getName() => $field->getValue($this->category)];
        }, [
            'PageId' => (string) $this->category->getCmsPageId(),
            'Master'        => (string) $this->category->getId(),
            'Name'          => (string) $this->category->getName(),
        ]);
    }

}
