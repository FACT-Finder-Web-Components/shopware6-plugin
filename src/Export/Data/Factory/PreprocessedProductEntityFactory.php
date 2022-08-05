<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data\Factory;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessorEntryReader;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity as ExportProductEntity;
use Omikron\FactFinder\Shopware6\Export\FieldsProvider;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class PreprocessedProductEntityFactory implements FactoryInterface
{
    private FactoryInterface $decoratedFactory;
    private FieldsProvider $fieldsProviders;
    private ExportSettings $exportSettings;
    private FeedPreprocessorEntryReader $feedPreprocessorReader;
    private iterable $cachedFields;

    public function __construct(
        FactoryInterface $decoratedFactory,
        FieldsProvider $fieldsProviders,
        ExportSettings $exportSettings,
        FeedPreprocessorEntryReader $feedPreprocessorReader,
        iterable $cachedFields
    ) {
        $this->decoratedFactory = $decoratedFactory;
        $this->fieldsProviders = $fieldsProviders;
        $this->exportSettings = $exportSettings;
        $this->feedPreprocessorReader = $feedPreprocessorReader;
        $this->cachedFields = $cachedFields;
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
//        if ($this->exportSettings->isExportCacheEnable() === false) {
//            yield from $this->decoratedFactory->createEntities($entity, $producedType);
//
//            return;
//        }

        $preprocessedFeeds = $this->feedPreprocessorReader->read($entity->getProductNumber());

        if ($preprocessedFeeds === []) {
            yield from $this->decoratedFactory->createEntities($entity, $producedType);

            return;
        }

        $fields = $this->fieldsProviders->getFields(get_class($entity));

        foreach ($entity->getChildren() as $child) {
            $feeds = $preprocessedFeeds[$child->getProductNumber()] ?? null;

            if (isset($feeds)) {
                $variant = new ExportProductEntity($child, $fields, $this->cachedFields);
                $variant->setFilterAttributes($feeds->getFilterAttributes());
                $variant->setCustomFields($feeds->getCustomFields());
                $variant->setAdditionalCache($feeds->getAdditionalCache());
                yield $variant;
            }
        }
    }
}
