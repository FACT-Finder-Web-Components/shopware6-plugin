<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\Field\ImageUrl;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Media\MediaEntity as Media;
use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaEntity as Cover;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class ImageUrlSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ImageUrl::class);
        $this->shouldBeAnInstanceOf(FieldInterface::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('ImageUrl');
    }

    function it_does_not_fail_if_the_image_is_not_present(Product $product, Cover $cover, Media $media)
    {
        $this->shouldNotThrow()->during('getValue', [$product]);
        $this->getValue($product)->shouldReturn('');

        $product->getCover()->willReturn($cover);
        $this->shouldNotThrow()->during('getValue', [$product]);
        $this->getValue($product)->shouldReturn('');

        $cover->getMedia()->willReturn($media);
        $media->getUrl()->willReturn('/product_image.jpg');
        $this->getValue($product)->shouldReturn('/product_image.jpg');
    }
}
