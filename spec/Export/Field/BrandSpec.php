<?php

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity as Manufacturer;

class BrandSpec extends ObjectBehavior
{
    function it_is_a_field()
    {
        $this->shouldBeAnInstanceOf(FieldInterface::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('Brand');
    }

    function it_does_not_fail_if_the_brand_is_not_present(Product $product, Manufacturer $manufacturer)
    {
        $this->shouldNotThrow()->during('getValue', [$product]);
        $this->getValue($product)->shouldReturn('');

        $product->getManufacturer()->willReturn($manufacturer);
        $manufacturer->getName()->willReturn(null);
        $this->shouldNotThrow()->during('getValue', [$product]);
        $this->getValue($product)->shouldReturn('');

        $product->getManufacturer()->willReturn($manufacturer);
        $manufacturer->getName()->willReturn('ACME Inc.');
        $this->shouldNotThrow()->during('getValue', [$product]);
        $this->getValue($product)->shouldReturn('ACME Inc.');
    }
}
