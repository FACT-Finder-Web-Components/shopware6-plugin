<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Communication;

use Omikron\FactFinder\Shopware6\Communication\Credentials;
use PhpSpec\ObjectBehavior;

class CredentialsSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('username', 'password');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Credentials::class);
    }

    function it_should_be_cast_to_string()
    {
        $this->shouldBeLike('Basic dXNlcm5hbWU6cGFzc3dvcmQ=');
    }
}
