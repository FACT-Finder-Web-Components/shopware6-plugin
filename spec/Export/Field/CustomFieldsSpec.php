<?php

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\Filter\TextFilter;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\Service\CustomFieldReadingData;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
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
        CustomFieldReadingData $customFieldReadingData
    ) {
        $languageRepository->search(Argument::type(Criteria::class), Argument::cetera())->will($this->mockLanguageRepository());
        $channelContext->getSalesChannel()->willReturn($this->getSalesChannel('2'));
        $salesChannelService->getSalesChannelContext()->willReturn($channelContext);
        $this->beConstructedWith(new PropertyFormatter(
            new TextFilter()),
            $salesChannelService,
            $customFieldRepository,
            $languageRepository,
            $exportSettings,
            $customFieldReadingData
        );
    }

    function it_is_a_field()
    {
        $this->shouldBeAnInstanceOf(FieldInterface::class);
    }

    function it_should_join_multiselect_option_values(
        Product $product,
        EntityRepositoryInterface $customFieldRepository,
        EntityRepositoryInterface $languageRepository
    ) {
        $customFieldRepository
            ->search(Argument::cetera())
            ->willReturn(
                $this->getCustomField(
                    'test-multi-select-field',
                    CustomFieldTypes::SELECT,
                    $this->selectFieldConfig
                ));

        $product->getTranslation('customFields')->willReturn(
            [
                'test-multi-select-field' => [
                    'option1',
                    'option2',
                ]
            ]);

        $this->getValue($product)->shouldReturn('|SelectFieldDE=option1DE#option2DE|');
    }

    function it_will_use_default_language_if_noone_is_stored_in_context(
        Product $product,
        EntityRepositoryInterface $customFieldRepository
    ) {
        $config = $this->selectFieldConfig;
        unset($config['label']['de-DE']);
        unset($config['options'][0]['label']['de-DE']);
        unset($config['options'][1]['label']['de-DE']);

        $customFieldRepository
            ->search(Argument::cetera())
            ->willReturn(
                $this->getCustomField(
                    'test-multi-select-field',
                    CustomFieldTypes::SELECT,
                    $config
                ));

        $product->getTranslation('customFields')->willReturn(
            [
                'test-multi-select-field' => [
                    'option1',
                    'option2',
                ]
            ]);

        $this->getValue($product)->shouldReturn('|SelectFieldEN=option1EN#option2EN|');
    }

    function it_will_return_label_technical_value_if_no_translation_is_provided(
        Product $product,
        EntityRepositoryInterface $customFieldRepository
    ) {
        $customFieldRepository
            ->search(Argument::cetera())
            ->willReturn(
                $this->getCustomField(
                    'test-multi-select-field',
                    CustomFieldTypes::SELECT,
                    [
                        'options' => [
                            ['value' => 'option1'],
                            ['value' => 'option2']
                        ]
                    ]
                ));

        $product->getTranslation('customFields')->willReturn(
            [
                'test-multi-select-field' => [
                    'option1',
                    'option2',
                ]
            ]);

        $this->getValue($product)->shouldReturn('|test-multi-select-field=option1#option2|');
    }

    private function getSalesChannel(string $languageId): SalesChannelEntity
    {
        $salesChannel = new SalesChannelEntity();
        $salesChannel->setLanguageId($languageId);
        return $salesChannel;
    }

    private function getCustomField(string $key, string $type, array $config): EntitySearchResult
    {
        $customField = new CustomFieldEntity();
        $customField->setType($type);
        $customField->setId($key);
        $customField->setConfig($config);
        return new EntitySearchResult('',1, new EntityCollection([$customField]), null, new Criteria(), new Context(new SystemSource()));
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
                return new EntitySearchResult('',1, new EntityCollection([$language]), null, new Criteria(), new Context(new SystemSource()));
            };
            return $getLanguage($args[0]->getIds()[0]);
        };
    }
}
