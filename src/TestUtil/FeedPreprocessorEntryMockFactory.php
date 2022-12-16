<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\TestUtil;

use Omikron\FactFinder\Shopware6\Export\FeedPreprocessorEntry;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;

class FeedPreprocessorEntryMockFactory
{
    public function create(ProductEntity $product, array $data = []): FeedPreprocessorEntry
    {
        $feedPreprocessorEntry = new FeedPreprocessorEntry();
        $feedPreprocessorEntry->setId($product->getId());
        $feedPreprocessorEntry->setProductNumber($product->getProductNumber());
        $feedPreprocessorEntry->setParentProductNumber($product->getParent()->getProductNumber());
        $feedPreprocessorEntry->setVariationKey($data['variationKey'] ?? '');
        $feedPreprocessorEntry->setFilterAttributes($data['filterAttributes'] ?? '');
        $feedPreprocessorEntry->setCustomFields($data['customFields'] ?? '');
        $feedPreprocessorEntry->setLanguageId($data['languageId'] ?? (new Context(new SystemSource()))->getLanguageId());
        $feedPreprocessorEntry->setAdditionalCache($data['additionalCache'] ?? []);

        return $feedPreprocessorEntry;
    }
}
