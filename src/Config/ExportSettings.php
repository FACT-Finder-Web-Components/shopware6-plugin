<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

class ExportSettings extends BaseConfig
{
    public function getDisabledPropertyGroups(): array
    {
        return $this->config('disabledPropertyGroups');
    }

    public function getDisabledCustomFields(): array
    {
        return $this->config('disabledCustomFields');
    }

    public function isMultiCurrencyPriceExportEnable(): bool
    {
        return $this->config('currencyPriceExport');
    }
}
