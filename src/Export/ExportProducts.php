<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity as ExportProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ExportProducts implements ExportInterface
{
    private SalesChannelRepository $productRepository;

    /** @var string[] */
    private array $customAssociations;

    public function __construct(SalesChannelRepository $productRepository, array $customAssociations)
    {
        $this->productRepository  = $productRepository;
        $this->customAssociations = $customAssociations;
    }

    public function getByContext(SalesChannelContext $context, int $batchSize = 100): iterable
    {
        $offset = 0;

        while (true) {
            $criteria = $this->getCriteria($batchSize, $offset);
            $products = $this->productRepository->search($criteria, $context);

            if ($products->count() === 0) {
                break;
            }

            foreach ($products->getElements() as $product) {
                yield $product;
            }

            $products->clear();
            unset($products, $criteria);
            gc_collect_cycles();

            // --- DEBUG PAMIĘCI START ---
            // Przeliczamy bajty na megabajty dla czytelności
            $memoryUsageMB = memory_get_usage(true) / 1024 / 1024;
            $peakMemoryMB  = memory_get_peak_usage(true) / 1024 / 1024;

            echo sprintf(
                "[%s] Offset: %d | Memory: %.2f MB | Peak: %.2f MB\n",
                date('H:i:s'),
                $offset,
                $memoryUsageMB,
                $peakMemoryMB
            );
            // --- DEBUG PAMIĘCI END ---

            $offset += $batchSize;
        }
    }

    public function getProducedExportEntityType(): string
    {
        return ExportProductEntity::class;
    }

    private function getCriteria(int $batchSize, int $offset): Criteria
    {
        $criteria = new Criteria();
        $criteria->setLimit($batchSize);
        $criteria->setOffset($offset);
        $criteria->addAssociation('categories');
        $criteria->addAssociation('categoriesRo');
        $criteria->addAssociation('manufacturer');
        $criteria->addAssociation('properties.group');
        $criteria->addAssociation('options.group');
        $criteria->addAssociation('media');
        $criteria->addAssociation('cover.media');
        $criteria->addAssociation('seoUrls');
        $criteria->addAssociation('customFields');

        $criteria->addAssociation('configuratorSettings.option.group');

        foreach ($this->customAssociations as $association) {
            $criteria->addAssociation($association);
        }

        // UWAGA: Usunęliśmy addFilter(new EqualsFilter('parentId', null));
        // Chcemy eksportować płasko wszystko: zarówno rodziców jak i warianty,
        // więc nie filtrujemy tutaj po parentId!

        return $criteria;
    }
}
