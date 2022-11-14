<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Data\Entity\CmsPageEntity;
use Omikron\FactFinder\Shopware6\Export\Filter\TextFilter;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use function Omikron\FactFinder\Shopware6\Internal\Utils\flatMap;
use function Omikron\FactFinder\Shopware6\Internal\Utils\safeGetByName;

class Layout implements FieldInterface
{
    private TextFilter $textFilter;

    public function __construct(TextFilter $textFilter)
    {
        $this->textFilter = $textFilter;
    }

    public function getName(): string
    {
        return 'Layout';
    }

    /**
     * @param CategoryEntity $entity
     *
     * @return string
     */
    public function getValue(Entity $entity): string
    {
        $cmsPage = $entity->getCmsPage();
        if (!$cmsPage) {
            return '';
        }

        $layout = array_map(
            fn (CmsSlotEntity $slot) => safeGetByName(safeGetByName($slot->getConfig(), 'content'), 'value'),
            flatMap(
                fn (CmsBlockEntity $block): array => $this->toValues($block->getSlots()),
                flatMap(
                    fn (CmsSectionEntity $section): array => $this->toValues($section->getBlocks()),
                    $this->toValues($cmsPage->getSections())
                )
            )
        );

        return $this->textFilter->filterValue(implode(' ', array_filter($layout)));
    }

    public function getCompatibleEntityTypes(): array
    {
        return [CmsPageEntity::class];
    }

    private function toValues(?EntityCollection $collection): array
    {
        if (!$collection) {
            return [];
        }
        /**
         * @param CmsBlockEntity|CmsSectionEntity $current
         * @param CmsBlockEntity|CmsSectionEntity $next
         *
         * @return bool
         */
        $sortByPosition = fn ($current, $next): bool => !method_exists($current, 'getPosition') || $current->getPosition() > $next->getPosition();
        $collection->sort($sortByPosition);

        return array_values($collection->getElements());
    }
}
