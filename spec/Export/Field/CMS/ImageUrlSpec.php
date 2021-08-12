<?php

namespace spec\Omikron\FactFinder\Shopware6\Export\Field\CMS;

use Omikron\FactFinder\Shopware6\Export\Field\CMS\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Shopware\Core\Content\Media\MediaEntity as Media;

class ImageUrlSpec extends ObjectBehavior
{
    function it_is_a_field()
    {
        $this->shouldBeAnInstanceOf(FieldInterface::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('ImageUrl');
    }

    function it_does_not_fail_if_the_image_is_not_present(Category $category, Media $media)
    {
        $this->shouldNotThrow()->during('getValue', [$category]);
        $this->getValue($category)->shouldReturn('');

        $category->getMedia()->willReturn($media);
        $media->getUrl()->willReturn('/category_image.jpg');
        $this->getValue($category)->shouldReturn('/category_image.jpg');
    }
}
