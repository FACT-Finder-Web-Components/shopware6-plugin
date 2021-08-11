<?php

namespace spec\Omikron\FactFinder\Shopware6\Export\Field\Brand;

use PhpSpec\ObjectBehavior;
use Omikron\FactFinder\Shopware6\Export\Field\Brand\FieldInterface;
use Shopware\Core\Content\Media\MediaEntity as Media;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity as Brand;

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
    function it_does_not_fail_if_the_image_is_not_present(Brand $brand, Media $media)
    {
        $this->shouldNotThrow()->during('getValue', [$brand]);
        $this->getValue($brand)->shouldReturn('');

        $brand->getMedia()->willReturn($media);
        $media->getUrl()->willReturn('/brand_image.jpg');
        $this->getValue($brand)->shouldReturn('/brand_image.jpg');
    }
}
