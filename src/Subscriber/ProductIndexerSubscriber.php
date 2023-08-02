<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Subscriber;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessor;
use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessorEntryPersister;
use Shopware\Core\Content\Product\Events\ProductIndexerEvent;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NandFilter;
use Shopware\Core\System\Language\LanguageCollection;
use Shopware\Core\System\Language\LanguageEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductIndexerSubscriber implements EventSubscriberInterface
{
    private EntityRepositoryInterface $productRepository;
    private EntityRepositoryInterface $languageRepository;
    private FeedPreprocessor $feedPreprocessor;
    private FeedPreprocessorEntryPersister $entryPersister;
    private ExportSettings $exportSettings;

    public function __construct(
        EntityRepositoryInterface $productRepository,
        EntityRepositoryInterface $languageRepository,
        FeedPreprocessor $feedPreprocessor,
        FeedPreprocessorEntryPersister $entryPersister,
        ExportSettings $exportSettings
    ) {
        $this->productRepository  = $productRepository;
        $this->languageRepository = $languageRepository;
        $this->feedPreprocessor   = $feedPreprocessor;
        $this->entryPersister     = $entryPersister;
        $this->exportSettings     = $exportSettings;
    }

    public static function getSubscribedEvents()
    {
        return [ProductIndexerEvent::class => 'processVariantsToExport'];
    }

    public function processVariantsToExport(ProductIndexerEvent $event): void
    {
        if ($this->exportSettings->isExportCacheEnable() === false) {
            return;
        }

        $languages = $this->fetchLanguages();
        foreach ($languages as $language) {
            $context  = $this->createLanguageContext($event, $language);
            $iterator = $this->getProductsIterator($event->getIds(), $context);

            while ($products = $iterator->fetch()) {
                foreach ($products as $product) {
                    $this->entryPersister->deleteAllProductEntries($product->getProductNumber(), $context);
                    $entries = $this->feedPreprocessor->createEntries($product, $context);
                    $this->entryPersister->insertProductEntries($entries, $context);
                }
            }
        }
    }

    private function createLanguageContext(ProductIndexerEvent $event, LanguageEntity $language): Context
    {
        return new Context(
            new SystemSource(),
            [],
            Defaults::CURRENCY,
            array_filter([$language->getId(), $language->getParentId(), Defaults::LANGUAGE_SYSTEM]),
            $event->getContext()->getVersionId()
        );
    }

    private function getProductsIterator(array $productIds, Context $context): RepositoryIterator
    {
        $context->setConsiderInheritance(true);
        $criteria = new Criteria($productIds);
        $criteria->addAssociation('configuratorGroupConfig');
        $criteria->addAssociation('children.options.group');
        return new RepositoryIterator($this->productRepository, $context, $criteria);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function fetchLanguages(): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new NandFilter([new EqualsFilter('salesChannelDomains.id', null)]));
        /** @var LanguageCollection $languages */
        $languages = $this->languageRepository->search($criteria, Context::createDefaultContext())->getEntities();

        return $languages->getElements();
    }
}
