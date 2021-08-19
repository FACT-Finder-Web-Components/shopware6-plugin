<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Export\Data\Entity\BrandEntity as ExportBrandEntity;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
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

    public function getCoveredEntityType(): string
    {
        return ProductManufacturerEntity::class;
    }

    public function getProducedExportEntityType(): string
    {
        return ExportBrandEntity::class;
    }

    private function getCriteria(int $batchSize): Criteria
    {
        $criteria = new Criteria();
        $criteria->setLimit($batchSize);
        $criteria->addAssociation('media');

        foreach ($this->customAssociations as $association) {
            $criteria->addAssociation($association);
        }

        return $criteria;
    }
}
