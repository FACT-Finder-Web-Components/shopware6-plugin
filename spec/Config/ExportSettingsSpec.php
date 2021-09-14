<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Config;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ExportSettingsSpec extends ObjectBehavior
{
    public function let(SystemConfigService $configService)
    {
        $this->beConstructedWith($configService);
    }

    public function it_will_cast_to_array_null_values(SystemConfigService $configService)
    {
        $configService->get('OmikronFactFinder.config.disabledCustomFields', Argument::any())->willReturn(null);
        $configService->get('OmikronFactFinder.config.disabledPropertyGroups', Argument::any())->willReturn(null);

        $this->getDisabledCustomFields()->shouldReturn([]);
        $this->getDisabledPropertyGroups()->shouldReturn([]);
    }

    public function it_will_cast_to_bool_null_values(SystemConfigService $configService)
    {
        $configService->get('OmikronFactFinder.config.currencyPriceExport', Argument::any())->willReturn(null);
        $this->isMultiCurrencyPriceExportEnable()->shouldReturn(false);
    }

    public function it_will_not_add_value_to_export_if_attribute_is_in_both_selects(SystemConfigService $configService)
    {
        $configService->get('OmikronFactFinder.config.disabledPropertyGroups', Argument::any())->willReturn(['test']);
        $configService->get('OmikronFactFinder.config.selectedNumericalAttributes', Argument::any())->willReturn(['test']);

        $this->getIgnoredFilteredValuesData()->shouldReturn(['test']);
        $this->getNumericalValuesColumnData()->shouldReturn([]);
    }

    public function it_add_value_to_numerical_column_if_attribute_is_not_ignored_and_in_numerical_select(SystemConfigService $configService)
    {
        $configService->get('OmikronFactFinder.config.disabledPropertyGroups', Argument::any())->willReturn(null);
        $configService->get('OmikronFactFinder.config.selectedNumericalAttributes', Argument::any())->willReturn(['test']);

        $this->getIgnoredFilteredValuesData()->shouldReturn(['test']);
        $this->getSelectedNumericalAttributes()->shouldReturn(['test']);
    }

    public function it_will_not_add_value_to_export_if_attribute_is_in_ignored_and_not_it_numerical_select(SystemConfigService $configService)
    {
        $configService->get('OmikronFactFinder.config.disabledPropertyGroups', Argument::any())->willReturn(['test']);
        $configService->get('OmikronFactFinder.config.selectedNumericalAttributes', Argument::any())->willReturn(null);

        $this->getIgnoredFilteredValuesData()->shouldReturn(['test']);
        $this->getSelectedNumericalAttributes()->shouldReturn([]);
    }

    public function it_will_add_value_to_filter_column_if_attribute_is_missing_in_both_selects(SystemConfigService $configService)
    {
        $configService->get('OmikronFactFinder.config.disabledPropertyGroups', Argument::any())->willReturn(null);
        $configService->get('OmikronFactFinder.config.selectedNumericalAttributes', Argument::any())->willReturn(null);

        $this->getIgnoredFilteredValuesData()->shouldReturn([]);
        $this->getSelectedNumericalAttributes()->shouldReturn([]);
    }

}
