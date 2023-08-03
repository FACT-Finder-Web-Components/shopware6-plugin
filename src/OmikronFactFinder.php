<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Data\Factory\FactoryInterface;
use Omikron\FactFinder\Shopware6\Export\ExportInterface;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Exception\PluginNotInstalledException;
use Shopware\Core\System\CustomField\Aggregate\CustomFieldSet\CustomFieldSetEntity;
use Shopware\Core\System\CustomField\CustomFieldEntity;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OmikronFactFinder extends Plugin
{
    public const FACT_FINDER_CUSTOM_FIELD_SET_NAME          = 'cms_export_include';
    public const CMS_EXPORT_INCLUDE_CUSTOM_FIELD_NAME       = 'ff_cms_export_include';
    public const DISABLE_SEARCH_IMMEDIATE_CUSTOM_FIELD_NAME = 'ff_cms_disable_search_immediate';

    private array $customFields = [
        [
            'name'   => self::CMS_EXPORT_INCLUDE_CUSTOM_FIELD_NAME,
            'type'   => CustomFieldTypes::BOOL,
            'config' => [
                'label'               => [
                    'en-GB' => 'Include in FACT-Finder® CMS Export',
                    'de-DE' => 'Include in FACT-Finder® CMS Export',
                ],
                'componentName'       => 'sw-field',
                'customFieldType'     => CustomFieldTypes::SWITCH,
                'customFieldPosition' => 1,
            ],
        ],
        [
            'name'   => self::DISABLE_SEARCH_IMMEDIATE_CUSTOM_FIELD_NAME,
            'type'   => CustomFieldTypes::BOOL,
            'config' => [
                'label'               => [
                    'en-GB' => 'Disable `ff-communication/search-immediate`',
                    'en-GB' => 'Disable `ff-communication/search-immediate`',
                ],
                'componentName'       => 'sw-field',
                'customFieldType'     => CustomFieldTypes::SWITCH,
                'customFieldPosition' => 2,
            ],
        ],
    ];

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->registerForAutoconfiguration(FieldInterface::class)->addTag('factfinder.export.field');
        $container->registerForAutoconfiguration(ExportInterface::class)->addTag('factfinder.export.exporter');
        $container->registerForAutoconfiguration(ExportEntityInterface::class)->addTag('factfinder.export.entity');
        $container->registerForAutoconfiguration(FactoryInterface::class)->addTag('factfinder.export.entity_factory');
    }

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $appContext = $installContext->getContext();

        $this->fixCMSExportIncludeFieldType($appContext);

        $factFinderFieldsSet = $this->getCustomFieldSet(self::FACT_FINDER_CUSTOM_FIELD_SET_NAME, $appContext);

        if (!$factFinderFieldsSet) {
            $this->installCustomFieldsSet([
               'name'      => self::FACT_FINDER_CUSTOM_FIELD_SET_NAME,
               'config'    => [
                   'label' => [
                       'de-DE' => 'FACT-Finder®',
                       'en-GB' => 'FACT-Finder®',
                   ],
               ],
               'relations' => [
                   [
                       'entityName' => 'category',
                   ],
               ],
           ], $appContext);
        }

        foreach ($this->customFields as $customField) {
            if (!$this->getCustomField($customField['name'], $appContext)) {
                $this->installCustomField($customField, $appContext, self::FACT_FINDER_CUSTOM_FIELD_SET_NAME);
            }
        }
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if (!$uninstallContext->keepUserData()) {
            $this->removeModuleData($uninstallContext);
        }

        parent::uninstall($uninstallContext);
    }

    private function removeModuleData(UninstallContext $uninstallContext): void
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $customFieldSet           = $this->getCustomFieldSet(self::FACT_FINDER_CUSTOM_FIELD_SET_NAME, $uninstallContext->getContext());

        if ($customFieldSet) {
            $customFieldSetRepository->delete([['id' => $customFieldSet->getId()]], $uninstallContext->getContext());
        }
    }

    private function getCustomFieldSet(string $name, Context $context): ?CustomFieldSetEntity
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $searchResult             = $customFieldSetRepository->search((new Criteria())->addFilter(new EqualsFilter('name', $name)), $context);

        return $searchResult->first();
    }

    private function getCustomField(string $name, Context $context): ?CustomFieldEntity
    {
        $customFieldsRepository = $this->container->get('custom_field.repository');
        $searchResult           = $customFieldsRepository->search((new Criteria())->addFilter(new EqualsFilter('name', $name)), $context);

        return $searchResult->first();
    }

    private function installCustomFieldsSet(array $data, Context $context): void
    {
        /** @var EntityRepository $customFielSetRepository */
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        $customFieldSetRepository->create([$data], $context);
    }

    private function installCustomField(array $data, Context $context, string $setName): void
    {
        /** @var EntityRepository $customFieldRepository */
        $customFieldsRepository = $this->container->get('custom_field.repository');
        $customFieldSet         = $this->getCustomFieldSet($setName, $context);
        if (!$customFieldSet) {
            throw new PluginNotInstalledException('omikron/shopware6-factfinder');
        }
        $customFieldsRepository->create([$data + ['customFieldSetId' => $customFieldSet->getId()]], $context);
    }

    /**
     * This function is introduced to fix FFWEB-2023.
     *
     * @param Context $context
     */
    private function fixCMSExportIncludeFieldType(Context $context): void
    {
        /** @var EntityRepository $customFieldRepository */
        $customFieldRepository = $this->container->get('custom_field.repository');

        $field = $this->getCustomField(OmikronFactFinder::CMS_EXPORT_INCLUDE_CUSTOM_FIELD_NAME, $context);
        if ($field instanceof CustomFieldEntity) {
            $customFieldRepository->update(
                [
                    [
                        'id'     => $field->getId(),
                        'type'   => CustomFieldTypes::BOOL,
                        'config' => [
                            'componentName'   => 'sw-field',
                            'customFieldType' => CustomFieldTypes::SWITCH,
                            'label'           => [
                                'en-GB' => 'Include in FACT-Finder® CMS Export',
                                'de-DE' => 'Include in FACT-Finder® CMS Export',
                            ],
                        ],
                    ],
                ], $context);
        }
    }
}
