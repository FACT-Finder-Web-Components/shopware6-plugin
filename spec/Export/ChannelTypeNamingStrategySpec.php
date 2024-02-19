<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export;

use PhpSpec\ObjectBehavior;

class ChannelTypeNamingStrategySpec extends ObjectBehavior
{
    public function it_will_use_all_parts_passed()
    {
        $this->createFeedFileName('products', 'my-channel')->shouldReturn('export.productData.my-channel.csv');
    }
}
