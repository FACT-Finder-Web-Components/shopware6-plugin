<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Formatter;

use PhpSpec\ObjectBehavior;

class NumberFormatterSpec extends ObjectBehavior
{
    public function it_cast_number_to_string()
    {
        $this->format(3)->shouldReturn('3.00');
    }

    public function it_should_respect_passed_precision()
    {
        $this->format(3, 0)->shouldReturn('3');
        $this->format(3, 1)->shouldReturn('3.0');
    }

    public function it_should_round_passed_values()
    {
        $this->format(3.1421)->shouldReturn('3.14');
    }
}
