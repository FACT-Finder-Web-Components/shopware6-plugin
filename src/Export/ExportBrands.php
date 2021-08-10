<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ExportBrands implements ExportInterface
{
    private EntityRepository $productManufacturerRepository;

    /** @var string[] */
    private array $customAssociations;

    public function __construct(EntityRepository $productManufacturerRepository, array $customAssociations)
    {
        $this->productManufacturerRepository = $productManufacturerRepository;
        $this->customAssociations            = $customAssociations;
    }

    /**
     * @param SalesChannelContext $context
     * @param int                 $batchSize
     *
     * @return ProductManufacturerEntity[]
     */
    public function getByContext(SalesChannelContext $context, int $batchSize = 100): iterable
    {
        $criteria      = $this->getCriteria($batchSize);
        $manufacturers = $this->productManufacturerRepository->search($criteria, new Context(new SystemSource()));
        while ($manufacturers->count()) {
            yield from $manufacturers;
            $criteria->setOffset($criteria->getOffset() + $criteria->getLimit());
            $manufacturers = $this->productManufacturerRepository->search($criteria, new Context(new SystemSource()));
        }
    }

    public function getCriteria(int $batchSize): Criteria
    {
        $criteria = new Criteria();
        $criteria->setLimit($batchSize);
        $criteria->addAssociation('mediaId');

        foreach ($this->customAssociations as $association) {
            $criteria->addAssociation($association);
        }

        return $criteria;
    }
}
