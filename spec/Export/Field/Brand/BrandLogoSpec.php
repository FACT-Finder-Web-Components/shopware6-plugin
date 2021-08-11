<?php

namespace spec\Omikron\FactFinder\Shopware6\Export\Field\Brand;

use PhpSpec\ObjectBehavior;
use Omikron\FactFinder\Shopware6\Export\Field\Brand\FieldInterface;

class BrandLogoSpec extends ObjectBehavior
{
    function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('BrandLogo');
    }
}
