<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class OmikronFactFinder extends Plugin
{
    private const CUSTOM_FIELD_NAME = 'cms_export_selector';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->registerForAutoconfiguration(FieldInterface::class)->addTag('factfinder.export.field');
    }

    //TODO: Should we add custom field on install or on plugin activation (activate method)
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        /** @var EntityRepositoryInterface $customFieldRepository */
        $customFieldRepository = $installContext->getPlugin()->container->get('custom_field_set.repository');

        $customFieldRepository->create([[
            'name' => self::CUSTOM_FIELD_NAME,
            'config' => [
                'label' => [
                    'de-DE' => 'FACT-Finder®',
                    'en-GB' => 'FACT-Finder®'
                ]
            ],
            'relations' => [[
                'entityName' => 'category'
            ]],
            'customFields' => [
                [
                    'name' => 'ff_cms_export',
                    'type' => CustomFieldTypes::SWITCH,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Include in FACT-Finder® CMS Export',
                            'de-DE' => 'Include in FACT-Finder® CMS Export'
                        ],
                        'customFieldPosition' => 1
                    ]
                ]
            ]
        ]], $installContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        //TODO: Should we keep custom field if user select an option Remove all app data permanently on plugin uninstall
        if ($uninstallContext->keepUserData()) {
            parent::uninstall($uninstallContext);

            return;
        }

        $this->removeCustomField($uninstallContext);
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
        $criteria->addFilter(new EqualsAnyFilter('name', [self::CUSTOM_FIELD_NAME]));

        $ids = $customFieldSetRepository->searchIds($criteria, $context);

        return $ids->getTotal() > 0 ? $ids : null;
    }
}
