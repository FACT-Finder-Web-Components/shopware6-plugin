<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

class ExportSettings extends BaseConfig
{
    public function getDisabledPropertyGroups()
    {
        return $this->config('disabledPropertyGroups');
    }

    public function getDisabledCustomFields()
    {
        return $this->config('disabledCustomFields');
    }

    public function isMultiCurrencyPriceExportEnable(): bool
    {
        return $this->config('currencyPriceExport');
    }
}
