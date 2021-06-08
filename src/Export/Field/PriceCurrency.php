<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\System\Currency\CurrencyEntity;

class PriceCurrency extends Price
{
    /** @var CurrencyEntity */
    private $currency;

    /**
     * @param CurrencyEntity $currency
     */
    public function __construct(CurrencyEntity $currency)
    {
        $this->currency = $currency;
    }

    public function getName(): string
    {
        return 'Price_' . $this->currency->getIsoCode();
    }

    public function getValue(Product $product): string
    {
        return round($product->getCalculatedPrice()->getTotalPrice() * $this->currency->getFactor(), 2);
    }
}
