<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Media\MediaEntity as Media;
use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaCollection;
use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class ImageUrlSpec extends ObjectBehavior
{
    public function it_is_a_field()
    {
        $this->shouldBeAnInstanceOf(FieldInterface::class);
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('ImageUrl');
    }

    public function it_does_not_fail_if_the_image_is_not_present(
        Product                $product,
        ProductMediaCollection $mediaCollection,
        ProductMediaEntity     $productMediaEntity,
        Media                  $media
    ) {
        $this->shouldNotThrow()->during('getValue', [$product]);
        $this->getValue($product)->shouldReturn('');

        $product->getMedia()->willReturn($mediaCollection);
        $mediaCollection->first()->willReturn($productMediaEntity);
        $productMediaEntity->getMedia()->willReturn($media);

        $media->getUrl()->willReturn('/product_image.jpg');
        $this->getValue($product)->shouldReturn('/product_image.jpg');
    }

    public function it_does_not_fail_if_media_is_not_a_collection(CategoryEntity $category, Media $media)
    {
        $category->getMedia()->willReturn($media);
        $media->getUrl()->willReturn('/category_image.jpg');
        $this->getValue($category)->shouldReturn('/category_image.jpg');
    }

    public function it_does_not_fail_if_media_first_is_null(Product $product, ProductMediaCollection $mediaCollection)
    {
        $product->getMedia()->willReturn($mediaCollection);
        $mediaCollection->first()->willReturn(null);

        $this->shouldNotThrow()->during('getValue', [$product]);
    }

    public function it_does_not_fail_if_media_is_null(Product $product)
    {
        $product->getMedia()->willReturn(null);

        $this->shouldNotThrow()->during('getValue', [$product]);
    }

}
