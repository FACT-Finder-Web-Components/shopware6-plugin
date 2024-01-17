<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\DataAbstractionLayer;

use Omikron\FactFinder\Shopware6\Events\FeedPreprocessorEntryBeforeCreate;
use Omikron\FactFinder\Shopware6\Export\Field\CustomFields as ExportCustomFields;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use function Omikron\FactFinder\Shopware6\Internal\Utils\flatMap;
use function Omikron\FactFinder\Shopware6\Internal\Utils\safeGetByName;

class FeedPreprocessor
{
    private PropertyFormatter $propertyFormatter;
    private EventDispatcherInterface $eventDispatcher;
    private ExportCustomFields $customFields;

    public function __construct(
        PropertyFormatter $propertyFormatter,
        EventDispatcherInterface $eventDispatcher,
        ExportCustomFields $customFields
    ) {
        $this->propertyFormatter = $propertyFormatter;
        $this->eventDispatcher   = $eventDispatcher;
        $this->customFields      = $customFields;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function createEntries(ProductEntity $product, Context $context): array
    {
        $noVisibleVariantsFilters = [];
        $visibleVariantsFilters   = [];
        $notVisibleGroups         = [];
        $entries                  = [];
        $customFields             = [];

        if ($product->getChildCount() === 0) {
            $customFields = explode('|', $this->customFields->getValue($product));
            $entry        = $this->formEntry($product, $context, implode('|', array_unique($customFields)));
            $event        = new FeedPreprocessorEntryBeforeCreate($entry, $context);
            $this->eventDispatcher->dispatch($event);

            return [$event->getEntry()];
        }

        $visibleGroupIds = $this->extractVisibleGroupIds($product);

        /** @var ProductEntity $child */
        foreach ($product->getChildren() as $child) {
            // fetch storefront presentation config for each variant
            $shouldGroupBeVisible = fn (string $groupId): bool => in_array($groupId, $visibleGroupIds);
            $variationKeyParts    = [];

            // loop over the options to collect those, that should be aggregated in exported products
            foreach ($child->getOptions() as $option) {
                $hasMainVariant = (bool) $product->getVariantListingConfig()->getMainVariantId();

                if (($shouldGroupBeVisible($option->getGroupId()) || $visibleGroupIds === []) && !$hasMainVariant) {
                    /*
                     * if a given option should be presented on storefront, we store its id
                     * This is used later to collect only unique combination of product variants
                     */
                    $variationKeyParts[] = $option->getId();

                    continue;
                }

                // if not then we format the option to a "ready to be exported" expression
                $notVisibleGroups[$option->getGroupId()][$option->getId()] = call_user_func($this->propertyFormatter, $option);
            }

            // collect all variants that should be visible used variation key created in a loop before
            $variationKey = implode('-', $variationKeyParts);

            $childCustomFields = explode('|', $this->customFields->getValue($child));

            foreach ($childCustomFields as $childCustomField) {
                $customFields[$variationKey][] = $childCustomField;
            }

            // store variant data to prevent iterating them again later
            $entries[$variationKey]                  = $this->formEntry($product, $context, $variationKey);
            $entries[$variationKey]['productNumber'] = $child->getProductNumber();

            $noVisibleVariantsFilters[$variationKey] = implode('|', flatMap(fn (
                array $groupOptions) => array_values($groupOptions), array_values($notVisibleGroups)));

            $visibleVariantsFilters[$variationKey] = implode('|', array_filter($child->getOptions()->map(fn (
                PropertyGroupOptionEntity $option): string => in_array($option->getGroupId(), $visibleGroupIds) ? call_user_func($this->propertyFormatter, $option) : '')));
        }

        return array_map(function (array $entry) use ($visibleVariantsFilters, $noVisibleVariantsFilters, $customFields, $context) {
            $variationKey              = $entry['variationKey'];
            $filters                   = implode('|', array_filter([$noVisibleVariantsFilters[$variationKey], $visibleVariantsFilters[$variationKey]]));
            $entry['filterAttributes'] = $filters ? "|$filters|" : '';
            $entry['customFields']     = array_filter($customFields[$variationKey]) ? sprintf('|%s|', trim(implode('|', array_unique($customFields[$variationKey])), '|')) : '';
            $event                     = new FeedPreprocessorEntryBeforeCreate($entry, $context);
            $this->eventDispatcher->dispatch($event);

            return $event->getEntry();
        }, $entries);
    }

    private function extractVisibleGroupIds(ProductEntity $product): array
    {
        //        $configuratorGroupConfig = $product->getConfiguratorGroupConfig();
        $configuratorGroupConfig = $product->getVariantListingConfig()->getConfiguratorGroupConfig();
        $hasMainVariant          = (bool) $product->getVariantListingConfig()->getMainVariantId();

        if (!$configuratorGroupConfig) {
            return $this->getVisibleGroupIdsFromChildren($product);
        }

        return array_reduce(
            array_filter(
                $product->getVariantListingConfig()->getConfiguratorGroupConfig(),
                fn (array $groupConfig): bool => !$hasMainVariant && (bool) safeGetByName($groupConfig, 'expressionForListings')
            ),
            fn (array $result, array $groupConfig): array => array_merge($result, [safeGetByName($groupConfig, 'id')]),
            []
        );
    }

    private function getVisibleGroupIdsFromChildren(ProductEntity $product)
    {
        $visibleGroupIds = [];

        foreach ($product->getChildren() as $child) {
            $options = $child->getOptions();

            if (isset($options)) {
                /** @var PropertyGroupOptionEntity $element */
                foreach ($options->getElements() as $element) {
                    $visibleGroupIds[] = $element->getGroupId();
                }
            }
        }

        return array_unique($visibleGroupIds);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function formEntry(
        ProductEntity $product,
        Context $context,
        ?string $variationKey = '',
        ?array $customFields = null,
        ?array $filterAttributes = null
    ): array {
        return [
            'id'                  => Uuid::randomHex(),
            'productNumber'       => $product->getProductNumber(),
            'variationKey'        => $variationKey,
            'parentProductNumber' => $product->getProductNumber(),
            'languageId'          => Uuid::fromHexToBytes($context->getLanguageId()),
            'filterAttributes'    => $filterAttributes ? "|$filterAttributes|" : '',
            'customFields'        => $customFields ? "|$customFields|" : '',
        ];
    }
}
