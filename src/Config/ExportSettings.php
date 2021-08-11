<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

class ExportSettings extends BaseConfig
{
    public function getSelectedFilterAttributes(): array
    {
        return $this->toArray($this->config('disabledPropertyGroups'));
    }

    public function getSelectedNumericalAttributes(): array
    {
        return $this->config('selectedNumericalAttributes') ?? [];
    }

    public function getSelectedNumericalAttributes(): array
    {
        return $this->config('selectedNumericalAttributes') ?? [];
    }

    public function getDisabledCustomFields(): array
    {
        return $this->toArray($this->config('disabledCustomFields'));
    }

    public function isMultiCurrencyPriceExportEnable(): bool
    {
        return (bool) $this->config('currencyPriceExport');
    }

    private function toArray(?array $value): array
    {
        return (array) $value;
    }
}
