<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity as Option;
use Shopware\Core\Content\Property\PropertyGroupEntity as Group;

class PropertyFormatterSpec extends ObjectBehavior
{
    function it_casts_values_to_string(FilterInterface $filter, Option $option, Group $group)
    {
        $this->beConstructedWith($filter);
        $filter->filterValue('')->willReturn('');
        $option->getGroup()->willReturn($group);
        $option->getName()->willReturn(null);
        $this->shouldNotThrow()->during('__invoke', [$option]);
    }
}
