<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ExportProducts
{
    /** @var SalesChannelRepositoryInterface */
    private $productRepository;

    public function __construct(SalesChannelRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param SalesChannelContext $context
     * @param int                 $batchSize
     *
     * @return SalesChannelProductEntity[]
     */
    public function getByContext(SalesChannelContext $context, int $batchSize = 100): iterable
    {
        $criteria = new Criteria();
        $criteria->setLimit($batchSize);
        $criteria->addAssociation('categories');
        $criteria->addAssociation('categoriesRo');
        $criteria->addAssociation('properties');
        $criteria->addAssociation('properties.group');
        $criteria->addAssociation('seoUrls');

        $products = $this->productRepository->search($criteria, $context);
        while ($products->count()) {
            yield from $products;
            $criteria->setOffset($criteria->getOffset() + $criteria->getLimit());
            $products = $this->productRepository->search($criteria, $context);
        }
    }
}
