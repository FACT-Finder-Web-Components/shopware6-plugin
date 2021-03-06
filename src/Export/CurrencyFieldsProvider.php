<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\Field\PriceCurrency;
use Omikron\FactFinder\Shopware6\Export\Formatter\NumberFormatter;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Currency\CurrencyEntity;

class CurrencyFieldsProvider
{
    /** @var EntityRepositoryInterface */
    private $currencyRepository;

    /** @var ExportSettings */
    private $exportSettings;

    /** @var array */
    private $currencyFields = [];

    public function __construct(EntityRepositoryInterface $currencyRepository, ExportSettings $exportSettings)
    {
        $this->currencyRepository  = $currencyRepository;
        $this->exportSettings      = $exportSettings;
    }

    public function getCurrencyFields(): array
    {
        if ($this->exportSettings->isMultiCurrencyPriceExportEnable()) {
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
