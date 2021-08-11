<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field\CMS;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\CustomFieldsService;
use Omikron\FactFinder\Shopware6\Export\Field\CustomFieldTrait;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomFields implements FieldInterface
{
    use CustomFieldTrait;

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
        return $this->getFieldValue($category);
    }
}
