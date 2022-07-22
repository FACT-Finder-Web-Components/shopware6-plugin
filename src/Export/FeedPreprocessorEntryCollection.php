<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(ExampleEntity $entity)
 * @method void               set(string $key, ExampleEntity $entity)
 * @method FeedPreprocessorEntry[]    getIterator()
 * @method FeedPreprocessorEntry[]    getElements()
 * @method FeedPreprocessorEntry|null get(string $key)
 * @method FeedPreprocessorEntry|null first()
 * @method FeedPreprocessorEntry|null last()
 */
class FeedPreprocessorEntryCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return FeedPreprocessorEntry::class;
    }
}
