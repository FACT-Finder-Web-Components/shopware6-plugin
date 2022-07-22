<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class FeedPreprocessorEntryDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'factfinder_feed_preprocessor';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return FeedPreprocessorEntry::class;
    }

    public function getCollectionClass(): string
    {
        return FeedPreprocessorEntryCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection(
            [
                (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
                (new StringField('language_id', 'languageId'))->addFlags(new Required()),
                (new StringField('product_number', 'productNumber'))->addFlags(new Required()),
                (new StringField('parent_product_number', 'parentProductNumber')),
                (new StringField('variation_key', 'variationKey')),
                (new StringField('filter_attributes', 'filterAttributes'))->addFlags(new Required()),
                (new StringField('custom_fields', 'customFields')),
                (new JsonField('additional_cache', 'additionalCache')),
            ]
        );
    }
}
