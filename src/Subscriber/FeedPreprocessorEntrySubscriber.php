<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Events\FeedPreprocessorEntryBeforeCreate;
use Omikron\FactFinder\Shopware6\Export\Field\CategoryPath;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FeedPreprocessorEntrySubscriber implements EventSubscriberInterface
{
    private CategoryPath $categoryFieldGenerator;
    private EntityRepositoryInterface $productRepository;

    public function __construct(
        EntityRepositoryInterface $productRepository,
        CategoryPath $categoryPath
    ) {
        $this->productRepository      = $productRepository;
        $this->categoryFieldGenerator = $categoryPath;
    }

    public static function getSubscribedEvents()
    {
        return [FeedPreprocessorEntryBeforeCreate::class => 'onCreateEntry'];
    }

    public function onCreateEntry(FeedPreprocessorEntryBeforeCreate $event): void
    {
        $entry    = $event->getEntry();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productNumber', $entry['productNumber']));
        $criteria->addAssociation('categories');
        $criteria->addAssociation('categoriesRo');
        $product                  = $this->productRepository->search($criteria, $event->getContext())->first();
        $categoryPath             = $this->categoryFieldGenerator->getValue($product);
        $entry['additionalCache'] = ['CategoryPath' => $categoryPath];
        $event->setEntry($entry);
    }
}
