<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity as ExportProductEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ExportProducts implements ExportInterface
{
    private SalesChannelRepositoryInterface $productRepository;

    /** @var string[] */
    private array $customAssociations;

    public function __construct(SalesChannelRepositoryInterface $productRepository, array $customAssociations)
    {
        $this->productRepository  = $productRepository;
        $this->customAssociations = $customAssociations;
    }

    /**
     * @param SalesChannelContext $context
     * @param int                 $batchSize
     *
     * @return SalesChannelProductEntity[]
     */
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

    public function getCriteria(int $batchSize): Criteria
    {
        $criteria = new Criteria();
        $criteria->setLimit($batchSize);
        $criteria->addAssociation('categories');
        $criteria->addAssociation('categoriesRo');
        $criteria->addAssociation('children.options.group');
        $criteria->addAssociation('manufacturer');
        $criteria->addAssociation('properties');
        $criteria->addAssociation('customFields');
        $criteria->addAssociation('properties.group');
        $criteria->addAssociation('seoUrls');
        foreach ($this->customAssociations as $association) {
            $criteria->addAssociation($association);
        }
        $criteria->addFilter(new EqualsFilter('parentId', null));
        return $criteria;
    }

    public function getCoveredEntityType(): string
    {
        return SalesChannelProductEntity::class;
    }

    public function getProducedExportEntityType(): string
    {
        return ExportProductEntity::class;
    }
}
