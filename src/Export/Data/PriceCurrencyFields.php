<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Data;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\Field\PriceCurrency;
use Omikron\FactFinder\Shopware6\Export\Formatter\NumberFormatter;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Currency\CurrencyEntity;

class PriceCurrencyFields
{
    /** @var EntityRepositoryInterface */
    private $currencyRepository;

    /** @var ExportSettings */
    private $exportFilters;

    /** @var array */
    private $currencyFields = [];

    /**
     * @param EntityRepositoryInterface $currencyRepository
     */
    public function __construct(EntityRepositoryInterface $currencyRepository, ExportSettings $exportFilters)
    {
        $this->currencyRepository = $currencyRepository;
        $this->exportFilters      = $exportFilters;
    }

    public function getCurrencyFields(): array
    {
        if ($this->exportFilters->isMultiCurrencyPriceExportEnable()) {
            if (empty($this->currencyFields)) {
                $this->currencyFields = $this->currencyRepository->search(new Criteria(), new Context(new SystemSource()))->map(
                    function (CurrencyEntity $currency) {
                        return new PriceCurrency($currency, new NumberFormatter());
                    }
                );
            }
        }

        return $this->currencyFields;
    }
}
