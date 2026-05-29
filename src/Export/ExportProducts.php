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
        $criteria = $this->getCriteria($batchSize);
        $products = $this->productRepository->search($criteria, $context);
        while ($products->count()) {
            yield from $products;
            $criteria->setOffset($criteria->getOffset() + $criteria->getLimit());
            $products = $this->productRepository->search($criteria, $context);
        }
    }

    public function getBatchByContext(SalesChannelContext $context, int $limit, int $offset): iterable
    {
        $criteria = $this->getCriteria($limit, $offset);
        $products = $this->productRepository->search($criteria, $context);

        foreach ($products->getElements() as $product) {
            yield $product;
        }

//        $products->clear();
//        unset($products, $criteria);
//        gc_collect_cycles();
    }

    public function getProducedExportEntityType(): string
    {
        return ExportProductEntity::class;
    }

    private function getCriteria(int $batchSize, ?int $offset = null): Criteria
    {
        $criteria = new Criteria();
        $criteria->setLimit($batchSize);

        if ($offset) {
            $criteria->setOffset($offset);
        }

        $criteria->addAssociation('categories');
        $criteria->addAssociation('categoriesRo');
        $criteria->addAssociation('children.options.group');
        $criteria->addAssociation('manufacturer');
        $criteria->addAssociation('properties');
        $criteria->addAssociation('customFields');
        $criteria->addAssociation('properties.group');
        $criteria->addAssociation('seoUrls');
        $criteria->addAssociation('media');
        $criteria->addAssociation('children.cover.media');
        foreach ($this->customAssociations as $association) {
            $criteria->addAssociation($association);
        }
        $criteria->addFilter(new EqualsFilter('parentId', null));

        return $criteria;
    }
}
