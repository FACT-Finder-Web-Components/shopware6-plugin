<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity as Manufacturer;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class BrandSpec extends ObjectBehavior
{
    public function it_is_a_field(): void
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    public function it_has_a_name(): void
    {
        $this->getName()->shouldReturn('Brand');
    }

    public function it_does_not_fail_if_the_brand_is_not_present(Product $product, Manufacturer $manufacturer): void
    {
        $this->shouldNotThrow()->during('getValue', [$product]);
        $this->getValue($product)->shouldReturn('');

        $product->getManufacturer()->willReturn($manufacturer);
        $this->shouldNotThrow()->during('getValue', [$product]);
        $this->getValue($product)->shouldReturn('');

        $manufacturer->getTranslation('name')->willReturn('ACME Inc.');
        $this->shouldNotThrow()->during('getValue', [$product]);
        $this->getValue($product)->shouldReturn('ACME Inc.');
    }

    public function it_gets_the_value_from_the_manufacturer(Product $product, Manufacturer $manufacturer): void
    {
        $product->getManufacturer()->willReturn($manufacturer);
        $manufacturer->getTranslation('name')->willReturn('FACT-Finder');
        $this->getValue($product)->shouldReturn('FACT-Finder');
    }
}
