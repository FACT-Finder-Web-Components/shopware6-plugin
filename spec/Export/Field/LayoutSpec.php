<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Filter\TextFilter;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSection\CmsSectionEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\CmsPageEntity;

class LayoutSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(new TextFilter());
    }

    function it_should_strip_html_tags(CategoryEntity $categoryEntity): void
    {
        $cmsPage = new CmsPageEntity();
        $cmsPage->setSections(
            new CmsSectionCollection(
                [
                    '1' => $this->createSection(
                        [
                            '1' => $this->createBlock(
                                [
                                    '1' => $this->createSlot(['content' => ['value' => '<h2>Lorem Ipsum </h2><p>Dolor sit amet</p>']]),
                                ]
                            ),
                        ]
                    ),
                ]
            )
        );
        $categoryEntity->getCmsPage()->willReturn($cmsPage);
        $this->getValue($categoryEntity)->shouldReturn('Lorem Ipsum Dolor sit amet');
    }

    function it_should_concatenate_values_from_all_slots_configs(CategoryEntity $categoryEntity): void
    {
        $cmsPage = new CmsPageEntity();
        $cmsPage->setSections(
            new CmsSectionCollection(
                [
                    '1' => $this->createSection(
                        [
                            '1' => $this->createBlock(
                                [
                                    '1' => $this->createSlot(['content' => ['value' => 'I am']]),
                                ], 1
                            ),
                        ],
                    ),
                    '2' => $this->createSection(
                        [
                            '1' => $this->createBlock(
                                [
                                    '1' => $this->createSlot(['content' => ['value' => 'concatenated']]),
                                ], 2
                            ),
                        ], 2
                    ),
                ]
            )
        );
        $categoryEntity->getCmsPage()->willReturn($cmsPage);
        $this->getValue($categoryEntity)->shouldReturn('I am concatenated');
    }

    function it_should_sort_output_by_position(CategoryEntity $categoryEntity): void
    {
        $cmsPage = new CmsPageEntity();
        $cmsPage->setSections(
            new CmsSectionCollection(
                [
                    '1' => $this->createSection(
                        [
                            '1' => $this->createBlock(
                                [
                                    '1' => $this->createSlot(['content' => ['value' => 'not']]),
                                ], 2
                            ),
                            '2' => $this->createBlock(
                                [
                                    '1' => $this->createSlot(['content' => ['value' => 'Yoda']]),
                                ],
                                3
                            ),
                            '3' => $this->createBlock(
                                [
                                    '1' => $this->createSlot(['content' => ['value' => 'I am']]),
                                ], 1
                            ),
                        ]
                    ),
                ]
            )
        );

        $categoryEntity->getCmsPage()->willReturn($cmsPage);
        $this->getValue($categoryEntity)->shouldReturn('I am not Yoda');
    }

    function it_should_not_throw_if_section_has_no_blocks(CategoryEntity $categoryEntity)
    {
        $cmsPage = new CmsPageEntity();
        $cmsPage->setSections(
            new CmsSectionCollection(
                [
                    '1' => $this->createSection(),
                    '2' => $this->createSection(),
                ]
            )
        );
        $categoryEntity->getCmsPage()->willReturn($cmsPage);
        $this->getValue($categoryEntity)->shouldReturn('');
    }

    function it_should_not_throw_if_category_has_no_layout_assigned(CategoryEntity $categoryEntity)
    {
        $categoryEntity->getCmsPage()->willReturn(null);
        $this->getValue($categoryEntity)->shouldReturn('');
    }

    function it_should_not_throw_if_block_has_no_slots(CategoryEntity $categoryEntity)
    {
        $cmsPage = new CmsPageEntity();
        $cmsPage->setSections(
            new CmsSectionCollection(
                [
                    '1' => $this->createSection(
                        [
                            '1' => $this->createBlock(),
                            '2' => $this->createBlock(),
                        ]
                    )
                ]
            )
        );

        $categoryEntity->getCmsPage()->willReturn($cmsPage);
        $this->getValue($categoryEntity)->shouldReturn('');
    }

    private function createSection(array $blocks = [], int $position = 1): CmsSectionEntity
    {
        $section = new CmsSectionEntity();
        $section->setId(uniqid());
        $section->setPosition($position);
        if (count($blocks)) {
            $section->setBlocks(new CmsBlockCollection($blocks));
        }
        return $section;
    }

    private function createBlock(array $slots = [], int $position = 1): CmsBlockEntity
    {
        $block = new CmsBlockEntity();
        $block->setId(uniqid());
        $block->setPosition($position);
        if (count($slots)) {
            $block->setSlots(new CmsSlotCollection($slots));
        }
        return $block;
    }

    private function createSlot(array $config): CmsSlotEntity
    {
        $slot = new CmsSlotEntity();
        $slot->setId(uniqid());
        $slot->setSlot(uniqid('slot-'));
        $slot->setConfig($config);
        return $slot;
    }
}
