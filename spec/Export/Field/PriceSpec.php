<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\Formatter\NumberFormatter;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceCollection;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class PriceSpec extends ObjectBehavior
{
    function let(NumberFormatter $numberFormatter)
    {
        $this->beConstructedWith($numberFormatter);
    }

    function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('Price');
    }

    function it_gets_the_calculated_product_price_if_no_calculated_prices(
        Product $product,
        CalculatedPrice $price,
        PriceCollection $priceCollection,
        NumberFormatter $numberFormatter
    ) {
        $product->getCalculatedPrices()->willReturn($priceCollection);
        $priceCollection->count()->willReturn(0);
        $product->getCalculatedPrice()->willReturn($price);
        $price->getTotalPrice()->willReturn(pi());
        $numberFormatter->format(pi())->willReturn('3.14');
        $this->getValue($product)->shouldReturn('3.14');
    }

    function it_gets_the_first_calculated_price_if_exist(
        Product $product,
        PriceCollection $priceCollection,
        CalculatedPrice $price,
        NumberFormatter $numberFormatter
    ) {
        $priceCollection->count()->willReturn(1);
        $product->getCalculatedPrices()->willReturn($priceCollection);
        $priceCollection->first()->willReturn($price);
        $price->getUnitPrice()->willReturn(pi());
        $numberFormatter->format(pi())->willReturn('3.14');
        $this->getValue($product)->shouldReturn('3.14');
    }
}
