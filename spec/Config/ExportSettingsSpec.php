<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Config;

use PhpSpec\ObjectBehavior;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ExportSettingsSpec extends ObjectBehavior
{
    public function let(SystemConfigService $configService)
    {
        $this->beConstructedWith($configService);
    }

    public function it_will_cast_to_array_null_values(SystemConfigService $configService)
    {
        $configService->get('OmikronFactFinder.config.disabledCustomFields')->willReturn(null);
        $configService->get('OmikronFactFinder.config.disabledPropertyGroups')->willReturn(null);

        $this->getDisabledPropertyGroups()->should->return([]);
        $this->getDisabledCustomFields()->should->return([]);
    }

    public function it_will_cast_to_bool_null_values(SystemConfigService $configService)
    {
        $configService->get('OmikronFactFinder.config.currencyPriceExport')->willReturn(null);
        $this->isMultiCurrencyPriceExportEnable()->should->return([]);
    }
}
