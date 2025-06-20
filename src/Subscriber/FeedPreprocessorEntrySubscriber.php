<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Events\FeedPreprocessorEntryBeforeCreate;
use Omikron\FactFinder\Shopware6\Export\Field\CategoryPath;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class FeedPreprocessorEntrySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityRepository $productRepository,
        private CategoryPath $categoryPath,
    ) {
    }

    public static function getSubscribedEvents(): array
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
        $categoryPath             = $this->categoryPath->getValue($product);
        $entry['additionalCache'] = ['CategoryPath' => $categoryPath];
        $event->setEntry($entry);
    }
}
