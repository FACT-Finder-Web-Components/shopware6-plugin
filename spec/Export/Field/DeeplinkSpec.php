<?php

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Seo\SeoUrl\SeoUrlCollection;
use Shopware\Core\Content\Seo\SeoUrl\SeoUrlEntity;

class DeeplinkSpec extends ObjectBehavior
{
    function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    function it_does_not_fail_if_seo_url_is_not_present(Product $product, SeoUrlCollection $seoUrls)
    {
        $product->getSeoUrls()->willReturn($seoUrls);
        $this->shouldNotThrow()->during('getValue', [$product]);
        $this->getValue($product)->shouldReturn('');
    }

    function it_use_first_seo_url_if_multiple_are_present(Product $product)
    {
        $product->getSeoUrls()->willReturn($this->prepareSeoUrlCollection());
        $this->shouldNotThrow()->during('getValue', [$product]);
        $this->getValue($product)->shouldReturn('/seo-path-1');
    }

    private function prepareSeoUrlCollection(): SeoUrlCollection
    {
        $urls = array_map(function (string $url): SeoUrlEntity {
            $seoUrl = new SeoUrlEntity();
            $seoUrl->setSeoPathInfo($url);
            $seoUrl->setId((string) rand(0,10));
            return $seoUrl;
        }, ['/seo-path-1','/seo-path-2']);
        return new SeoUrlCollection($urls);
    }
}
