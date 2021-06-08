<?php


namespace Omikron\FactFinder\Shopware6\Export\Data;

use Omikron\FactFinder\Shopware6\Config\ExportFilters;
use Omikron\FactFinder\Shopware6\Export\Field\PriceCurrency;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Currency\CurrencyEntity;

class PriceCurrencyFields
{
    /** @var EntityRepositoryInterface */
    private $currencyRepository;

    /** @var ExportFilters */
    private $exportFilters;

    /** @var array */
    private $currencyFields = [];

    /**
     * @param EntityRepositoryInterface $currencyRepository
     */
    public function __construct(EntityRepositoryInterface $currencyRepository, ExportFilters $exportFilters)
    {
        $this->currencyRepository = $currencyRepository;
        $this->exportFilters = $exportFilters;
    }

    public function getCurrencyFields(): array
    {
        if ($this->exportFilters->getCurrencyPriceExportValue()) {
            if (empty($this->currencyFields)) {
                $this->currencyFields = $this->currencyRepository->search(new Criteria(), new Context(new SystemSource()))->map(
                    function (CurrencyEntity $currency) {
                        return new PriceCurrency($currency);
                    }
                );
            }
        }

        return $this->currencyFields;
    }
}
