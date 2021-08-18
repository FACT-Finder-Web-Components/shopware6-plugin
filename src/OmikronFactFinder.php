<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6;

use Omikron\FactFinder\Shopware6\Export\Data\ExportEntityInterface;
use Omikron\FactFinder\Shopware6\Export\Data\Factory\FactoryInterface;
use Omikron\FactFinder\Shopware6\Export\ExportInterface;
use Omikron\FactFinder\Shopware6\Export\Field\Brand\FieldInterface as FieldInterfaceBrand;
use Omikron\FactFinder\Shopware6\Export\Field\CMS\FieldInterface as FieldInterfaceCMS;
use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OmikronFactFinder extends Plugin
{
    public const CMS_EXPORT_INCLUDE_CUSTOM_FIELD_SET_NAME = 'cms_export_include';
    public const CMS_EXPORT_INCLUDE_CUSTOM_FIELD_NAME     = 'ff_cms_export_include';

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
        /** @var EntityRepositoryInterface $customFieldRepository */
        $customFieldRepository = $installContext->getPlugin()->container->get('custom_field_set.repository');

        if (!$this->customFieldsExist($installContext->getContext())) {
            $customFieldRepository->create([[
                'name'   => self::CMS_EXPORT_INCLUDE_CUSTOM_FIELD_SET_NAME,
                'config' => [
                    'label' => [
                        'de-DE' => 'FACT-Finder®',
                        'en-GB' => 'FACT-Finder®',
                    ],
                ],
                'relations' => [[
                    'entityName' => 'category',
                ]],
                'customFields' => [
                    [
                        'name'   => self::CMS_EXPORT_INCLUDE_CUSTOM_FIELD_NAME,
                        'type'   => CustomFieldTypes::SWITCH,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Include in FACT-Finder® CMS Export',
                                'de-DE' => 'Include in FACT-Finder® CMS Export',
                            ],
                            'customFieldPosition' => 1,
                        ],
                    ],
                ],
            ]], $installContext->getContext());
        }
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if (!$uninstallContext->keepUserData()) {
            $this->removeCustomField($uninstallContext);
        }

        parent::uninstall($uninstallContext);
    }

    private function removeCustomField(UninstallContext $uninstallContext): void
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $fieldIds = $this->customFieldsExist($uninstallContext->getContext());

        if ($fieldIds) {
            $customFieldSetRepository->delete(array_values($fieldIds->getData()), $uninstallContext->getContext());
        }
    }

    private function customFieldsExist(Context $context): ?IdSearchResult
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('name', [self::CMS_EXPORT_INCLUDE_CUSTOM_FIELD_SET_NAME]));

        $ids = $customFieldSetRepository->searchIds($criteria, $context);

        return $ids->getTotal() > 0 ? $ids : null;
    }
}
