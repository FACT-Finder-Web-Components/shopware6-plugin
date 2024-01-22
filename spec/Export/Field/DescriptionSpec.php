<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class DescriptionSpec extends ObjectBehavior
{
    public function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    public function it_should_return_the_description(Product $product)
    {
        $product->getTranslation('description')->willReturn('FACT-Finder Web Components');
        $this->getValue($product)->shouldReturn('FACT-Finder Web Components');
    }

    public function it_should_not_throw_errors(Product $product)
    {
        $this->shouldNotThrow()->during('getValue', [$product]);
    }
}
