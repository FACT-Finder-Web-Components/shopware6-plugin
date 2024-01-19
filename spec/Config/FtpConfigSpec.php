<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Config;

use PhpSpec\ObjectBehavior;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class FtpConfigSpec extends ObjectBehavior
{
    public function let(SystemConfigService $configService): void
    {
        $this->beConstructedWith($configService);
    }

    public function it_should_use_channel_name_as_part_of_uploaded_file_name(SystemConfigService $configService): void
    {
        $salesChannelId = '1';
        $channel        = 'ff_channel_name';
        $configService->get('OmikronFactFinder.config.channel', $salesChannelId)->willReturn($channel);
        $this->getUploadFileName($salesChannelId)->shouldContain($channel);
    }
}
