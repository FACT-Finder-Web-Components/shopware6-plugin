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
        $configService->get('FactFinder.config.disabledCustomFields', Argument::any())->willReturn(null);
        $configService->get('FactFinder.config.disabledPropertyGroups', Argument::any())->willReturn(null);

        $this->getDisabledCustomFields()->shouldReturn([]);
        $this->getDisabledPropertyGroups()->shouldReturn([]);
    }

    public function it_will_cast_to_bool_null_values(SystemConfigService $configService)
    {
        $configService->get('FactFinder.config.currencyPriceExport', Argument::any())->willReturn(null);
        $this->isMultiCurrencyPriceExportEnable()->shouldReturn(false);
    }
}
