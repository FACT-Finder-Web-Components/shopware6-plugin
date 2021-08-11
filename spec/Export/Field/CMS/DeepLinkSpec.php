<?php


namespace spec\Omikron\FactFinder\Shopware6\Export\Field\CMS;

use Omikron\FactFinder\Shopware6\Export\Field\CMS\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Category\CategoryEntity as Category;
use Shopware\Core\Content\Seo\SeoUrl\SeoUrlCollection;
use Shopware\Core\Content\Seo\SeoUrl\SeoUrlEntity;


class DeepLinkSpec extends ObjectBehavior
{
    function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    function it_does_not_fail_if_seo_url_is_not_present(Category $category, SeoUrlCollection $seoUrls)
    {
        $category->getSeoUrls()->willReturn($seoUrls);
        $this->shouldNotThrow()->during('getValue', [$category]);
        $this->getValue($category)->shouldReturn('');
    }

    function it_use_first_seo_url_if_multiple_are_present(Category $category)
    {
        $category->getSeoUrls()->willReturn($this->prepareSeoUrlCollection());
        $this->shouldNotThrow()->during('getValue', [$category]);
        $this->getValue($category)->shouldReturn('/seo-path-1');
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
