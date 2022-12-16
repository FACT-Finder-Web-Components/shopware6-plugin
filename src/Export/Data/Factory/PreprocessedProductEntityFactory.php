<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Factory;

use ArrayIterator;
use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessor;
use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessorEntryPersister;
use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessorEntryReader;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity as ExportProductEntity;
use Omikron\FactFinder\Shopware6\Export\FeedPreprocessorEntry;
use Omikron\FactFinder\Shopware6\Export\FieldsProvider;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Traversable;

class PreprocessedProductEntityFactory implements FactoryInterface
{
    private SalesChannelService $channelService;
    private FactoryInterface $decoratedFactory;
    private FieldsProvider $fieldsProviders;
    private ExportSettings $exportSettings;
    private FeedPreprocessorEntryReader $feedPreprocessorReader;
    private FeedPreprocessorEntryPersister $entryPersister;
    private FeedPreprocessor $feedPreprocessor;
    private iterable $cachedFields;

    public function __construct(
        FactoryInterface $decoratedFactory,
        SalesChannelService $channelService,
        FieldsProvider $fieldsProviders,
        ExportSettings $exportSettings,
        FeedPreprocessorEntryReader $feedPreprocessorReader,
        FeedPreprocessorEntryPersister $entryPersister,
        FeedPreprocessor $feedPreprocessor,
        Traversable $cachedFields
    ) {
        $this->channelService         = $channelService;
        $this->decoratedFactory       = $decoratedFactory;
        $this->fieldsProviders        = $fieldsProviders;
        $this->exportSettings         = $exportSettings;
        $this->feedPreprocessorReader = $feedPreprocessorReader;
        $this->entryPersister         = $entryPersister;
        $this->feedPreprocessor       = $feedPreprocessor;
        $this->cachedFields           = $cachedFields;
    }

    public function handle(Entity $entity): bool
    {
        return in_array(get_class($entity), [SalesChannelProductEntity::class]);
    }

    /**
     * @return ExportProductEntity[]|iterable
     */
    public function createEntities(Entity $entity, string $producedType = ExportProductEntity::class): iterable
    {
        if ($this->exportSettings->isExportCacheEnable() === false) {
            yield from $this->decoratedFactory->createEntities($entity, $producedType);

            return;
        }

        $salesChannelcontext = $this->channelService->getSalesChannelContext();
        $context             = $salesChannelcontext->getContext();
        $preprocessedEntries = $this->feedPreprocessorReader->read(
            $entity->getProductNumber(),
            $salesChannelcontext->getLanguageId()
        );

        if ($preprocessedEntries === []) {
            $this->entryPersister->insertProductEntries($this->feedPreprocessor->createEntries($entity, $context), $context);

            yield from $this->decoratedFactory->createEntities($entity, $producedType);

            return;
        }

        if ($entity->getChildCount() === 0) {
            $exportProduct = $this->getExportProduct($entity, $entity, $preprocessedEntries);

            if (isset($exportProduct)) {
                yield $exportProduct;
            }

            return;
        }

        foreach ($entity->getChildren() as $child) {
            $exportProduct = $this->getExportProduct($child, $entity, $preprocessedEntries);

            if (isset($exportProduct)) {
                yield $exportProduct;
            }
        }
    }

    /**
     * @param FeedPreprocessorEntry[] $preprocessedEntries
     */
    private function getExportProduct(SalesChannelProductEntity $entity, ProductEntity $parent, array $preprocessedEntries): ?ExportProductEntity
    {
        $fields = $this->fieldsProviders->getFields(SalesChannelProductEntity::class);
        $cache  = $preprocessedEntries[$entity->getProductNumber()] ?? null;

        if (isset($cache)) {
            $exportProduct = new ExportProductEntity($entity, new ArrayIterator($fields), $this->cachedFields);
            $exportProduct->setFilterAttributes($cache->getFilterAttributes());
            $exportProduct->setCustomFields($cache->getCustomFields());
            $exportProduct->setAdditionalCache(new ArrayIterator($cache->getAdditionalCache()));
            $exportProduct->setParent($parent);

            return $exportProduct;
        }

        return null;
    }
}
