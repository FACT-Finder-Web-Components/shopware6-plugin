<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Formatter\NumberFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\System\Currency\CurrencyEntity;

class PriceCurrency extends Price
{
    /** @var CurrencyEntity */
    private $currency;

    /** @var NumberFormatter */
    private $numberFormatter;

    public function __construct(CurrencyEntity $currency, NumberFormatter $numberFormatter)
    {
        $this->currency        = $currency;
        $this->numberFormatter = $numberFormatter;
    }

    public function getName(): string
    {
        return 'Price_' . $this->currency->getIsoCode();
    }

    public function getValue(Product $product): string
    {
        return $this->numberFormatter->format($product->getCalculatedPrice()->getTotalPrice() * $this->currency->getFactor());
    }
}
