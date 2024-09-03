<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\Field\PriceCurrency;
use Omikron\FactFinder\Shopware6\Export\Formatter\NumberFormatter;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Currency\CurrencyEntity;

class CurrencyFieldsProvider
{
    private EntityRepository $currencyRepository;
    private ExportSettings $exportSettings;
    private NumberFormatter $numberFormatter;
    private array $currencyFields = [];

    public function __construct(
        EntityRepository $currencyRepository,
        ExportSettings $exportSettings,
        NumberFormatter $numberFormatter,
    ) {
        $this->currencyRepository = $currencyRepository;
        $this->exportSettings     = $exportSettings;
        $this->numberFormatter    = $numberFormatter;
    }

    public function getCurrencyFields(): array
    {
        if ($this->exportSettings->isMultiCurrencyPriceExportEnable()) {
            if (empty($this->currencyFields)) {
                $this->currencyFields = $this->currencyRepository
                    ->search(new Criteria(), new Context(new SystemSource()))
                    ->map(fn (CurrencyEntity $currency): PriceCurrency => new PriceCurrency($currency, $this->numberFormatter));
            }
        }

        return $this->currencyFields;
    }
}
