<?php

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity as Manufacturer;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class BrandSpec extends ObjectBehavior
{
    function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('Brand');
    }

    function it_does_not_fail_if_the_brand_is_not_present(Entity $entity, Manufacturer $manufacturer)
    {
        $this->shouldNotThrow()->during('getValue', [$entity]);
        $this->getValue($entity)->shouldReturn('');

        $entity->getManufacturer()->willReturn($manufacturer);
        $this->shouldNotThrow()->during('getValue', [$entity]);
        $this->getValue($entity)->shouldReturn('');

        $manufacturer->getTranslation('name')->willReturn('ACME Inc.');
        $this->shouldNotThrow()->during('getValue', [$entity]);
        $this->getValue($entity)->shouldReturn('ACME Inc.');
    }

    function it_gets_the_value_from_the_manufacturer(Entity $entity, Manufacturer $manufacturer)
    {
        $entity->getManufacturer()->willReturn($manufacturer);
        $manufacturer->getTranslation('name')->willReturn('FACT-Finder');
        $this->getValue($entity)->shouldReturn('FACT-Finder');
    }
}
