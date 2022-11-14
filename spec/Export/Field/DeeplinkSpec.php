<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Seo\Event\SeoUrlUpdateEvent;
use Shopware\Core\Content\Seo\SeoUrl\SeoUrlCollection;
use Shopware\Core\Content\Seo\SeoUrl\SeoUrlEntity;
use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Storefront\Framework\Seo\SeoUrlRoute\NavigationPageSeoUrlRoute as CategoryRoute;

class DeeplinkSpec extends ObjectBehavior
{
    public function let(SeoUrlUpdater $seoUrlUpdater)
    {
        $this->beConstructedWith($seoUrlUpdater);
    }

    public function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    public function it_use_first_seo_url_if_multiple_are_present(Product $product)
    {
        $product->getSeoUrls()->willReturn($this->prepareSeoUrlCollection());
        $this->shouldNotThrow()->during('getValue', [$product]);
        $this->getValue($product)->shouldReturn('/seo-path-1');
    }

    public function it_will_call_indexer_if_no_url_is_available(
        Product $product,
        Category $category,
        SeoUrlUpdater $seoUrlUpdater
    ) {
        $product->getSeoUrls()->willReturn(new SeoUrlCollection());
        $product->getId()->willReturn(1);

        $category->getSeoUrls()->willReturn(new SeoUrlCollection());
        $category->getId()->willReturn(2);

        $this->onUrlUpdated(new SeoUrlUpdateEvent([0 => ['seoPathInfo' => '/some-seo-url-link']]));
        $seoUrlUpdater->update(CategoryRoute::ROUTE_NAME, ['1'])->shouldBeCalled();
        $seoUrlUpdater->update(CategoryRoute::ROUTE_NAME, ['2'])->shouldBeCalled();

        $this->getValue($product);
        $this->getValue($category);
    }

    public function it_will_export_seo_path_from_fetch_callback(Product $product)
    {
        $product->getId()->willReturn(1);
        $this->onUrlUpdated(new SeoUrlUpdateEvent([0 => ['seoPathInfo' => '/some-seo-url-link']]));
        $product->getSeoUrls()->willReturn(new SeoUrlCollection());
        $this->getValue($product)->shouldReturn('/some-seo-url-link');
    }

    private function prepareSeoUrlCollection(): SeoUrlCollection
    {
        $urls = array_map(function (string $url): SeoUrlEntity {
            $seoUrl = new SeoUrlEntity();
            $seoUrl->setSeoPathInfo($url);
            $seoUrl->setId($url);
            return $seoUrl;
        }, ['/seo-path-1', '/seo-path-2']);
        return new SeoUrlCollection($urls);
    }
}
