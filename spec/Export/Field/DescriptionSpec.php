<?php

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class DescriptionSpec extends ObjectBehavior
{
    function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    function it_should_return_the_description(Product $product)
    {
        $product->getDescription()->willReturn('FACT-Finder Web Components');
        $this->getValue($product)->shouldReturn('FACT-Finder Web Components');
    }

    function it_should_not_throw_errors(Product $product)
    {
        $this->shouldNotThrow()->during('getValue', [$product]);
    }
}
