<?php

namespace spec\Omikron\FactFinder\Shopware6\Config;

use PhpSpec\ObjectBehavior;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class FtpConfigSpec extends ObjectBehavior
{
    function let(SystemConfigService $configService)
    {
        $this->beConstructedWith($configService);
    }

    function it_should_use_channel_name_as_part_of_uploaded_file_name(SystemConfigService $configService)
    {
        $salesChannelId = '1';
        $channel        = 'ff_channel_name';
        $configService->get('OmikronFactFinder.config.channel', $salesChannelId)->willReturn($channel);

        $this->getUploadFileName($salesChannelId)->shouldContain($channel);
    }
}
