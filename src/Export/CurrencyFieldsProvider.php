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
    private EntityRepositoryInterface $currencyRepository;

    private ExportSettings $exportSettings;

    private array $currencyFields = [];

    public function __construct(EntityRepositoryInterface $currencyRepository, ExportSettings $exportSettings)
    {
        $this->currencyRepository = $currencyRepository;
        $this->exportSettings     = $exportSettings;
    }

    public function getCurrencyFields(): array
    {
        if ($this->exportSettings->isMultiCurrencyPriceExportEnable()) {
            if (empty($this->currencyFields)) {
                $this->currencyFields = $this->currencyRepository
                    ->search(new Criteria(), new Context(new SystemSource()))
                    ->map(fn (CurrencyEntity $currency): PriceCurrency => new PriceCurrency($currency, new NumberFormatter()));
            }
        }

        return $this->currencyFields;
    }
}
