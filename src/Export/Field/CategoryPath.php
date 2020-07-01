<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;

class CategoryPath implements FieldInterface
{
    /** @var SalesChannelRepositoryInterface */
    private $categoryRepository;

    /** @var SalesChannelService */
    private $channelService;

    /** @var string */
    private $fieldName;

    public function __construct(
        SalesChannelRepositoryInterface $categoryRepository,
        SalesChannelService $channelService,
        string $fieldName = 'CategoryPath'
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->channelService     = $channelService;
        $this->fieldName          = $fieldName;
    }

    public function getName(): string
    {
        return $this->fieldName;
    }

    public function getValue(SalesChannelProductEntity $product): string
    {
        return implode('/', array_filter(array_map(function (string $categoryId) {
            $criteria = new Criteria([$categoryId]);
            $criteria->addAssociation('translations');
            $category = $this->categoryRepository->search($criteria, $this->channelService->getSalesChannelContext())->first();
            return $category ? $category->getName() : '';
        }, $product->getCategoryTree())));
    }
}
