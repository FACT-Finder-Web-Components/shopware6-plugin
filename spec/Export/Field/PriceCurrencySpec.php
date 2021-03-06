<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Field\FieldInterface;
use Omikron\FactFinder\Shopware6\Export\Formatter\NumberFormatter;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\System\Currency\CurrencyEntity;

class PriceCurrencySpec extends ObjectBehavior
{
    function let(NumberFormatter $numberFormatter, CurrencyEntity $currencyEntity)
    {
        $this->beConstructedWith($currencyEntity, $numberFormatter);
    }

    function it_is_a_field()
    {
        $this->shouldHaveType(FieldInterface::class);
    }

    function it_has_a_name(CurrencyEntity $currencyEntity)
    {
        $currencyEntity->getIsoCode()->willReturn('EUR');
        $this->getName()->shouldReturn('Price_EUR');
    }

    function it_gets_the_product_price_in_a_given_currency(Product $product, CalculatedPrice $price, NumberFormatter $numberFormatter, CurrencyEntity $currencyEntity)
    {
        $currencyEntity->getFactor()->willReturn(pi());
        $product->getCalculatedPrice()->willReturn($price);
        $price->getTotalPrice()->willReturn(pi());
        $numberFormatter->format(pow(pi(),2))->willReturn('9.86');
        $this->getValue($product)->shouldReturn('9.86');
    }
}
