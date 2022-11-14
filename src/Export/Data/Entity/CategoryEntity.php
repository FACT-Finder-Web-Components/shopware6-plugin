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
    private iterable $fields;

    public function __construct(ShopwareCategoryEntity $category, iterable $fields)
    {
        $this->category              = $category;
        $this->fields                = $fields;
    }

    public function getId(): string
    {
        return $this->category->getId();
    }

    public function toArray(): array
    {
        return array_reduce(
            $this->fields,
            fn (
                array $fields,
                FieldInterface $field): array => $fields + [$field->getName() => $field->getValue($this->category)],
            [
                'Id'          => $this->category->getId() ?? '',
                'Name'        => $this->category->getName() ?? '',
            ]);
    }
}
