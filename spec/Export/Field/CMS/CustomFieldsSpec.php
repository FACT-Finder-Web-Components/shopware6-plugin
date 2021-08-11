<?php


namespace spec\Omikron\FactFinder\Shopware6\Export\Field\CMS;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\CustomFieldsService;
use Omikron\FactFinder\Shopware6\Export\Filter\TextFilter;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\CustomField\CustomFieldEntity;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\Locale\LocaleEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Omikron\FactFinder\Shopware6\Export\Field\CMS\FieldInterface;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class CustomFieldsSpec extends ObjectBehavior
{
    private $selectFieldConfig = [
        'label'   =>
            [
                'de-DE' => 'SelectFieldDE',
                'en-GB' => 'SelectFieldEN',
            ],
        'options' =>
            [
                [
                    'label' =>
                        [
                            'de-DE' => 'option1DE',
                            'en-GB' => 'option1EN',
                        ],
                    'value' => 'option1',
                ],
                [
                    'label' =>
                        [
                            'de-DE' => 'option2DE',
                            'en-GB' => 'option2EN',
                        ],
                    'value' => 'option2',
                ],
            ],
    ];

    function let(
        SalesChannelService $salesChannelService,
        SalesChannelContext $channelContext,
        EntityRepositoryInterface $customFieldRepository,
        EntityRepositoryInterface $languageRepository,
        ExportSettings $exportSettings,
        CustomFieldsService $customFieldsService,
        Category $category
    ) {
        $languageRepository->search(Argument::type(Criteria::class), Argument::cetera())->will($this->mockLanguageRepository());
        $channelContext->getSalesChannel()->willReturn($this->getSalesChannel('2'));
        $salesChannelService->getSalesChannelContext()->willReturn($channelContext);
        $exportSettings->getDisabledCustomFields()->willReturn([]);
        $category->getTranslation('customFields')->willReturn(
            [
                'test-multi-select-field' => [
                    'option1',
                    'option2',
                ]
            ]);
        $this->beConstructedWith(
            new PropertyFormatter(new TextFilter()),
            $salesChannelService,
            $customFieldRepository,
            $languageRepository,
            $exportSettings,
            $customFieldsService
        );
    }

    function it_is_a_field()
    {
        $this->shouldBeAnInstanceOf(FieldInterface::class);
    }

    function it_should_join_multiselect_option_values(
        Category $category,
        EntityRepositoryInterface $customFieldRepository,
        ExportSettings $exportSettings
    ) {
        $customFieldRepository
            ->search(Argument::cetera())
            ->willReturn(
                $this->toSearchEntityResult(
                    [
                        $this->getCustomField(
                            'test-multi-select-field',
                            CustomFieldTypes::SELECT,
                            $this->selectFieldConfig
                        )
                    ]
                ));


        $exportSettings->getDisabledCustomFields()->willReturn([]);
        $this->getValue($category)->shouldReturn('|SelectFieldDE=option1DE#option2DE|');
    }

    function it_will_use_default_language_if_none_is_stored_in_context(
        Category $category,
        EntityRepositoryInterface $customFieldRepository
    )
    {
        $config = $this->selectFieldConfig;
        unset($config['label']['de-DE']);
        unset($config['options'][0]['label']['de-DE']);
        unset($config['options'][1]['label']['de-DE']);

        $customFieldRepository
            ->search(Argument::cetera())
            ->willReturn(
                $this->toSearchEntityResult(
                    [
                        $this->getCustomField(
                            'test-multi-select-field',
                            CustomFieldTypes::SELECT,
                            $config
                        )
                    ]
                ));

        $this->getValue($category)->shouldReturn('|SelectFieldEN=option1EN#option2EN|');
    }

    function it_will_return_label_technical_value_if_no_translation_is_provided(
        Category $category,
        EntityRepositoryInterface $customFieldRepository
    )
    {
        $customFieldRepository
            ->search(Argument::cetera())
            ->willReturn(
                $this->toSearchEntityResult(
                    [
                        $this->getCustomField(
                            'test-multi-select-field',
                            CustomFieldTypes::SELECT,
                            [
                                'options' => [
                                    ['value' => 'option1'],
                                    ['value' => 'option2']
                                ]
                            ]
                        )
                    ]
                ));

        $this->getValue($category)->shouldReturn('|test-multi-select-field=option1#option2|');
    }

    function it_will_skip_disabled_custom_fields(
        Category $category,
        EntityRepositoryInterface $customFieldRepository, ExportSettings $exportSettings, CustomFieldsService $customFieldsService)
    {
        $customFieldRepository
            ->search(Argument::cetera())
            ->willReturn(
                $this->toSearchEntityResult(
                    [
                        $this->getCustomField(
                            'test-enabled',
                            CustomFieldTypes::SELECT,
                            [
                                'label' => [
                                    'en-GB' => 'Enabled attribute',
                                ],
                                'options' => [[
                                    'label' => ['en-GB' => 'exported value'],
                                    'value' => 'i should be exported',
                                ]],
                            ]
                        ),
                        $this->getCustomField(
                            'test-disabled',
                            CustomFieldTypes::SELECT,
                            [
                                'label' => ['en-GB' => 'Disabled attribute'],
                                'options' => [[
                                    'label' => ['en-GB' => 'not exported value'],
                                    'value' => 'i should not be exported',
                                ]]
                            ]
                        )
                    ]
                ));

        $category->getTranslation('customFields')->willReturn(
            [
                'test-enabled' => ['i should be exported']
            ],
            [
                'test-disabled' => 'i should not be exported'
            ]

        );
        $exportSettings->getDisabledCustomFields()->willReturn(['test-disabled']);
        $customFieldsService->getCustomFieldNames(['test-disabled'])->willReturn(['Disabled attribute']);
        $this->getValue($category)->shouldReturn('|Enabled attribute=exported value|');
    }

    private function toSearchEntityResult(array $entities)
    {
        $size     = is_array($entities) ? count($entities) : 1;
        $elements = is_array($entities) ? $entities : [$entities];
        return new EntitySearchResult('', 1, new EntityCollection($elements), null, new Criteria(), new Context(new SystemSource()));
    }

    private function getCustomField(string $key, string $type, array $config): CustomFieldEntity
    {
        $customField = new CustomFieldEntity();
        $customField->setType($type);
        $customField->setId($key);
        $customField->setConfig($config);
        return $customField;
    }

    private function getSalesChannel(string $languageId): SalesChannelEntity
    {
        $salesChannel = new SalesChannelEntity();
        $salesChannel->setLanguageId($languageId);
        return $salesChannel;
    }

    private function mockLanguageRepository(): callable
    {
        return function ($args) {
            $getLanguage = function (string $languageId): EntitySearchResult {
                $language = new LanguageEntity();
                $language->setId($languageId);
                $locale = new LocaleEntity();
                $locale->setCode($languageId === Defaults::LANGUAGE_SYSTEM ? 'en-GB' : 'de-DE');
                $language->setLocale($locale);
                return new EntitySearchResult(
                    '',
                    1,
                    new EntityCollection([$language]), null, new Criteria(), new Context(new SystemSource())
                );
            };
            return $getLanguage($args[0]->getIds()[0]);
        };
    }
}
