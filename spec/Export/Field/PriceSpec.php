<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\Formatter\NumberFormatter;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class PriceSpec extends ObjectBehavior
{
    public function let(NumberFormatter $numberFormatter)
    {
        $this->beConstructedWith($numberFormatter);
    }

    public function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('Price');
    }

    public function it_gets_the_product_price(Product $product, CalculatedPrice $price, NumberFormatter $numberFormatter)
    {
        $product->getCalculatedPrice()->willReturn($price);
        $price->getTotalPrice()->willReturn(pi());
        $numberFormatter->format(pi())->willReturn('3.14');
        $this->getValue($product)->shouldReturn('3.14');
    }
}
