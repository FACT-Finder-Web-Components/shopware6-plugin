<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\CustomField\CustomFieldEntity;

class CustomFieldsService
{
    private EntityRepository $customFieldRepository;
    private array $cachedFields;

    public function __construct(EntityRepository $customFieldRepository)
    {
        $this->customFieldRepository = $customFieldRepository;
    }

    public function getCustomFieldNames(array $customFieldIds): array
    {
        $key = implode('', $customFieldIds);

        if (!isset($this->cachedFields[$key])) {
            $searchResult = $this->customFieldRepository->search(
                new Criteria($customFieldIds),
                new Context(new SystemSource())
            );
            $this->cachedFields[$key] = $searchResult->map(fn (CustomFieldEntity $customField): string => $customField->getName());
        }

        return $this->cachedFields[$key];
    }
}
