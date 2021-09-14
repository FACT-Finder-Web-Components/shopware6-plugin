<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Config;

class ExportSettings extends BaseConfig
{
    public function getDisabledPropertyGroups(): array
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

    public function getIgnoredFilteredValuesData(): array
    {
        return array_unique(array_merge($this->getDisabledPropertyGroups(), $this->getSelectedNumericalAttributes()));
    }

    public function getNumericalValuesColumnData(): array
    {
        return array_diff($this->getSelectedNumericalAttributes(), $this->getDisabledPropertyGroups());
    }

    private function toArray(?array $value): array
    {
        return (array) $value;
    }
}
