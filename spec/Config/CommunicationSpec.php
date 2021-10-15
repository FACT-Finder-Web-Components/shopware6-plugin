<?php

namespace spec\Omikron\FactFinder\Shopware6\Config;

use PhpSpec\ObjectBehavior;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class CommunicationSpec extends ObjectBehavior
{
    function let(SystemConfigService $configService)
    {
        $this->beConstructedWith($configService);
    }

    function it_should_cast_credentials_to_string()
    {
        $this->getCredentials()->shouldContainOnlyStrings();
    }

    function it_should_return_factfinder_channel_configured_for_specific_saleschannel(SystemConfigService $configService)
    {
        $configService->get('OmikronFactFinder.config.channel', '1')->willReturn('channel_1');
        $configService->get('OmikronFactFinder.config.channel', '2')->willReturn('channel_2');

        $this->getChannel('1')->shouldReturn('channel_1');
        $this->getChannel('2')->shouldReturn('channel_2');
    }

    public function getMatchers(): array
    {
        return [
            'containOnlyStrings' => function ($subject) {
                return count(array_filter($subject, 'is_string')) === count($subject);
            },
        ];
    }
}
