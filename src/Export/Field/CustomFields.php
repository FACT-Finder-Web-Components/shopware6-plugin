<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use InvalidArgumentException;
use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\CustomFieldsService;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\CmsPageEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\OmikronFactFinder;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\CustomField\CustomFieldEntity;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Shopware\Core\System\Language\LanguageEntity;
use function array_map as map;
use function Omikron\FactFinder\Shopware6\Internal\Utils\flatMap;

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

    public function getValue(Entity $entity): string
    {
        $value = $this->getFieldValueAsArray($entity);

        return $value ? '|' . implode('|', $value) . '|' : '';
    }

    public function getValueAsKeyValueArray(Entity $entity): array
    {
        return array_reduce(
            $this->getFieldValueAsArray($entity),
            function (array $carriedValues, string $customFieldValue) {
                $separatorPosition   = strpos($customFieldValue, '=');
                $key                 = substr($customFieldValue, 0, $separatorPosition);
                $value               = substr($customFieldValue, $separatorPosition + 1, strlen($customFieldValue));
                $carriedValues[$key] = $value;

                return $carriedValues;
            },
            []
        );
    }

    public function getCompatibleEntityTypes(): array
    {
        return [ProductEntity::class, CmsPageEntity::class];
    }

    private function getFieldValueAsArray(Entity $entity): array
    {
        $fields        = $this->getFields($entity);
        $usedLocale    = $this->findLanguage($this->salesChannelService->getSalesChannelContext()->getSalesChannel()->getLanguageId())->getLocale()->getCode();
        $defaultLocale = $this->findLanguage(Defaults::LANGUAGE_SYSTEM)->getLocale()->getCode();

        $translatedFields = flatMap(
            $this->toTranslatedKeyValuePairs($usedLocale, $defaultLocale),
            array_keys($fields),
            array_values($fields)
        );

        return map([$this->propertyFormatter, 'format'], array_keys($translatedFields), array_values($translatedFields));
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function toTranslatedKeyValuePairs(string $usedLocale, string $defaultLocale): callable
    {
        $formatManyValues = fn ($value)                => is_array($value) ? implode('#', $value) : $value;
        $formatLabel      = fn (array $option): string => array_key_exists('label', $option) && count($option['label']) > 0
            ? ($option['label'][$usedLocale] ?? $option['label'][$defaultLocale] ?? $option['value'])
            : $option['value'];

        /*
         * @param string $key
         * @param string|array $storedValue
         *
         * @return array
         */
        return function (string $key, $storedValue) use ($formatManyValues, $formatLabel): array {
            try {
                $customField = $this->getCustomField($key);
                $config      = $customField->getConfig();

                //select types not necessarily must have 'options', entity selectors don't have it
                if ($customField->getType() === CustomFieldTypes::SELECT && isset($config['options'])) {
                    $options = array_filter($config['options'], fn (
                        array $option): bool => is_array($storedValue) ? in_array($option['value'], $storedValue) : $option['value'] === $storedValue);
                    $translatedOptionValue = $formatManyValues(map($formatLabel, $options));
                }

                $label = $formatLabel(['value' => $key] + $config);

                return [$label => $translatedOptionValue ?? $formatManyValues($storedValue)];
            } catch (InvalidArgumentException $e) {
                return [$key => $formatManyValues($storedValue)];
            }
        };
    }

    private function getFields(Entity $entity): array
    {
        $customFields = $entity->getTranslation('customFields') ?? [];

        if (!empty($customFields)) {
            if (!empty($this->exportSettings->getDisabledCustomFields())) {
                $excludedCustomFields = $this->customFieldsService->getCustomFieldNames($this->exportSettings->getDisabledCustomFields());
                $customFields         = array_diff_key($customFields, array_flip($excludedCustomFields));
            }
        }

        if ($entity instanceof CategoryEntity) {
            unset($customFields[OmikronFactFinder::CMS_EXPORT_INCLUDE_CUSTOM_FIELD_NAME]);
        }

        return $customFields;
    }

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
