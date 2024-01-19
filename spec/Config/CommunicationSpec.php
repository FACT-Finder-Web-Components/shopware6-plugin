<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Config;

use PhpSpec\ObjectBehavior;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class CommunicationSpec extends ObjectBehavior
{
    public function let(SystemConfigService $configService): void
    {
        $this->beConstructedWith($configService);
    }

    public function it_should_cast_credentials_to_string(): void
    {
        $this->getCredentials()->shouldContainOnlyStrings();
    }

    public function it_should_return_factfinder_channel_configured_for_specific_saleschannel(SystemConfigService $configService): void
    {
        $configService->get('OmikronFactFinder.config.channel', '1')->willReturn('channel_1');
        $configService->get('OmikronFactFinder.config.channel', '2')->willReturn('channel_2');

        $this->getChannel('1')->shouldReturn('channel_1');
        $this->getChannel('2')->shouldReturn('channel_2');
    }

    public function getMatchers(): array
    {
        return [
            'containOnlyStrings' => fn ($subject) => count(array_filter($subject, 'is_string')) === count($subject),
        ];
    }
}
