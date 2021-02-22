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

    public function getMatchers(): array
    {
        return [
            'containOnlyStrings' => function ($subject) {
                return count(array_filter($subject, 'is_string')) === count($subject);
            },
        ];
    }
}
