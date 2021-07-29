<?php


namespace Omikron\FactFinder\Shopware6\Export;


use Omikron\FactFinder\Shopware6\OmikronFactFinder;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ExportCategories implements ExportInterface
{
    private SalesChannelRepositoryInterface $categoryRepository;

    /** @var string[] */
    private array $customAssociations;

    public function __construct(SalesChannelRepositoryInterface $categoryRepository, array $customAssociations)
    {
        $this->categoryRepository = $categoryRepository;
        $this->customAssociations = $customAssociations;
    }

    public function getByContext(SalesChannelContext $context, int $batchSize = 100): iterable
    {
        $criteria = $this->getCriteria($batchSize);
        $categories = $this->categoryRepository->search($criteria, $context);

        while ($categories->count()) {
            yield from $categories;
            $criteria->setOffset($criteria->getOffset() + $criteria->getLimit());
            $categories = $this->categoryRepository->search($criteria, $context);
        }
    }

    public function getCriteria(int $batchSize): Criteria
    {
        $criteria = new Criteria();
        $criteria->setLimit($batchSize);
        $criteria->addAssociation('customFields');
        $criteria->addAssociation('seoUrls');
        foreach ($this->customAssociations as $association) {
            $criteria->addAssociation($association);
        }
        $criteria->addFilter(new EqualsFilter(sprintf('customFields.%s', OmikronFactFinder::CUSTOM_FIELD_NAME), true));

        return $criteria;
    }

}
