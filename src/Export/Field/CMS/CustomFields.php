<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;

use InvalidArgumentException;
use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\CustomFieldsService;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\OmikronFactFinder;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\CustomField\CustomFieldEntity;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Shopware\Core\System\Language\LanguageEntity;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomFields implements FieldInterface
{
    private PropertyFormatter $propertyFormatter;
    private SalesChannelService $salesChannelService;
    private EntityRepositoryInterface $customFieldRepository;
    private EntityRepositoryInterface $languageRepository;
    private ExportSettings $exportSettings;
    private CustomFieldsService $customFieldsService;
    private array $loadedFields = [];

    public function __construct(
        PropertyFormatter $propertyFormatter,
        SalesChannelService $salesChannelService,
        EntityRepositoryInterface $customFieldRepository,
        EntityRepositoryInterface $languageRepository,
        ExportSettings $exportSettings,
        CustomFieldsService $customFieldsService
    ) {
        $this->propertyFormatter     = $propertyFormatter;
        $this->salesChannelService   = $salesChannelService;
        $this->customFieldRepository = $customFieldRepository;
        $this->languageRepository    = $languageRepository;
        $this->exportSettings        = $exportSettings;
        $this->customFieldsService   = $customFieldsService;
    }

    public function getName(): string
    {
        return 'CustomFields';
    }

    public function getValue(Category $category): string
    {
        $fields           = $this->getCategoryFields($category);
        $translatedFields = array_merge([], ...array_map($this->customFieldConfig(), array_keys($fields), array_values($fields)));
        $value            = array_map([$this->propertyFormatter, 'format'], array_keys($translatedFields), array_values($translatedFields));

        return $value ? '|' . implode('|', $value) . '|' : '';
    }

    private function customFieldConfig()
    {
        $usedLocale    = $this->findLanguage($this->salesChannelService->getSalesChannelContext()->getSalesChannel()->getLanguageId())->getLocale()->getCode();
        $defaultLocale = $this->findLanguage(Defaults::LANGUAGE_SYSTEM)->getLocale()->getCode();

        /*
         * @param string $key
         * @param string|array $storedValue
         *
         * @return array
         */
        return function (string $key, $storedValue) use ($usedLocale, $defaultLocale) {
            try {
                $customField = $this->getCustomField($key);
            } catch (\InvalidArgumentException $e) {
                return [$key => $storedValue];
            }

            if ($customField->getType() === CustomFieldTypes::SELECT) {
                $options = array_filter($customField->getConfig()['options'], function (array $option) use ($storedValue) {
                    return is_array($storedValue) ? in_array($option['value'], $storedValue) : $option['value'] === $storedValue;
                });

                $translatedOptionValue = implode('#', array_map(function (array $option) use ($usedLocale, $defaultLocale): string {
                    return array_key_exists('label', $option) && count($option['label']) > 0
                        ? $option['label'][$usedLocale] ?? $option['label'][$defaultLocale]
                        : $option['value'];
                }, $options));
            }

            $label = $customField->getConfig() !== null && array_key_exists('label', $customField->getConfig())
                ? ($customField->getConfig()['label'][$usedLocale] ?? $customField->getConfig()['label'][$defaultLocale])
                : $key;

            return [$label => $translatedOptionValue ?? $storedValue];
        };
    }

    private function getCategoryFields(Category $category): array
    {
        $categoryCustomFields = $category->getTranslation('customFields') ?? [];

        if (!empty($categoryCustomFields)) {
            if (!empty($this->exportSettings->getDisabledCustomFields())) {
                $excludedCustomFields  = $this->customFieldsService->getCustomFieldNames($this->exportSettings->getDisabledCustomFields());
                $categoryCustomFields  = array_diff_key($categoryCustomFields, array_flip($excludedCustomFields));
            }
        }

        unset($categoryCustomFields[OmikronFactFinder::CMS_EXPORT_INCLUDE_CUSTOM_FIELD_NAME]);

        return $categoryCustomFields;
    }

    /**
     * @param string $key
     *
     * @return CustomFieldEntity
     */
    private function getCustomField(string $key): CustomFieldEntity
    {
        if (!isset($this->loadedFields[$key])) {
            $customField = $this->customFieldRepository->search(
                (new Criteria())->addFilter(new EqualsFilter('name', $key)),
                new Context(new SystemSource())
            )->first();
            if (!$customField) {
                throw new InvalidArgumentException('There is no custom field with a given key');
            }
            $this->loadedFields[$key] = $customField;
        }
        return $this->loadedFields[$key];
    }

    private function findLanguage(string $languageId): LanguageEntity
    {
        $criteria = new Criteria([$languageId]);
        $criteria->addAssociation('locale');

        return $this->languageRepository->search(
            $criteria,
            new Context(new SystemSource())
        )->first();
    }
}
