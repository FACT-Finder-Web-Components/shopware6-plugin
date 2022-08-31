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
        PropertyFormatter        $propertyFormatter,
        EventDispatcherInterface $eventDispatcher,
        ExportCustomFields       $customFields
    ) {
        $this->propertyFormatter = $propertyFormatter;
        $this->eventDispatcher = $eventDispatcher;
        $this->customFields = $customFields;
    }

    public function createEntries(ProductEntity $product, Context $context): array
    {
        $filtersFromNotVisibleVariants = [];
        $filtersFromVisibleVariants = [];
        $notVisibleGroups = [];
        $entries = [];
        $customFields = [];

        $visibleGroupIds = $this->extractVisibleGroupIds($product);

        /** @var \Shopware\Core\Content\Product\ProductEntity $child */
        foreach ($product->getChildren() as $child) {
            //fetch storefront presentation config for each variant
            $shouldGroupBeVisible = fn(string $groupId): bool => in_array($groupId, $visibleGroupIds);
            $variationKeyParts = [];

            //loop over the options to collect those, that should be aggregated in exported products
            foreach ($child->getOptions() as $option) {
                if ($shouldGroupBeVisible($option->getGroupId()) || $visibleGroupIds === []) {
                    /**
                     * if a given option should be presented on storefront, we store its id
                     * This is used later to collect only unique combination of product variants
                     */
                    $variationKeyParts[] = $option->getId();
                } else {
                    //if not then we format the option to a "ready to be exported" expression
                    $notVisibleGroups[$option->getGroupId()][$option->getId()] = call_user_func($this->propertyFormatter, $option);
                }
            }

            //collect all variants that should be visible used variation key created in a loop before
            $variationKey = implode('-', $variationKeyParts);

            $childCustomFields = explode('|', trim($this->customFields->getValue($child), '|'));

            foreach ($childCustomFields as $childCustomField) {
                $customFields[$variationKey][] = $childCustomField;
            }

            //store variant data to prevent iterating them again later

            $entries[$variationKey] = [
                'id' => Uuid::randomHex(),
                'productNumber' => $child->getProductNumber(),
                'variationKey' => $variationKey,
                'parentProductNumber' => $product->getProductNumber(),
                'languageId' => Uuid::fromHexToBytes($context->getLanguageId()),
                'additionalCache' => []
            ];

            $filtersFromNotVisibleVariants[$variationKey] = implode('|', flatMap(fn(
                array $groupOptions) => array_values($groupOptions), array_values($notVisibleGroups)));

            $filtersFromVisibleVariants[$variationKey] = implode('|', array_filter($child->getOptions()->map(fn(
                PropertyGroupOptionEntity $option): string => in_array($option->getGroupId(), $visibleGroupIds) ? call_user_func($this->propertyFormatter, $option) : '')));
        }

        return array_map(function (array $entry) use ($filtersFromVisibleVariants, $filtersFromNotVisibleVariants, $customFields, $context) {
            $variationKey = $entry['variationKey'];
            $entry['filterAttributes'] = implode('|', [$filtersFromNotVisibleVariants[$variationKey], $filtersFromVisibleVariants[$variationKey]]);
            $entry['customFields'] = sprintf('|%s|', implode('|', array_unique($customFields[$variationKey])));
            $event = new FeedPreprocessorEntryBeforeCreate($entry, $context);
            $this->eventDispatcher->dispatch($event);

            return $event->getEntry();
        }, $entries);
    }

    private function extractVisibleGroupIds(ProductEntity $product): array
    {
        $configuratorGroupConfig = $product->getConfiguratorGroupConfig();
        $hasMainVariant = (bool) $product->getMainVariantId();

        if (!$configuratorGroupConfig) {
            return [];
        }
        return array_reduce(
            array_filter($product->getConfiguratorGroupConfig(),
                fn(
                    array $groupConfig): bool => !$hasMainVariant && (bool) safeGetByName($groupConfig, 'expressionForListings')),
            fn(
                array $result,
                array $groupConfig): array => array_merge($result, [safeGetByName($groupConfig, 'id')]), []
        );
    }
}
