<?php


namespace Omikron\FactFinder\Shopware6\Service;


use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\CustomField\CustomFieldEntity;

class CustomFieldReadingData
{
    /** @var EntityRepositoryInterface  */
    private $customFieldRepository;
    private $cachedFields;
    /**
     * CustomFieldReadingData constructor.
     * @param EntityRepositoryInterface $customFieldRepository
     */
    public function __construct(EntityRepositoryInterface $customFieldRepository)
    {
        $this->customFieldRepository = $customFieldRepository;
    }

    public function getCustomFieldNames(array $customFieldIds): array
    {
        $key = implode('', $customFieldIds);

        if (!isset($this->cachedFields[$key])) {
            /** @var EntitySearchResult $data */
            $searchResult = $this->customFieldRepository->search(
                new Criteria($customFieldIds),
                new Context(new SystemSource())
            );

            $this->cachedFields[$key] = $searchResult->map(function (CustomFieldEntity $customField) {
                return $customField->getName();
            });
        }

        return $this->cachedFields[$key];
    }
}
