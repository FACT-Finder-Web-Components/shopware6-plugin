<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Formatter\NumberFormatter;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\System\Currency\CurrencyEntity;

class PriceCurrency extends Price
{
    private CurrencyEntity $currency;
    private NumberFormatter $numberFormatter;

    public function __construct(CurrencyEntity $currency, NumberFormatter $numberFormatter)
    {
        $this->currency        = $currency;
        $this->numberFormatter = $numberFormatter;
    }

    public function getName(): string
    {
        return 'Price_' . $this->currency->getIsoCode();
    }

    public function getValue(Entity $entity): string
    {
        return $this->numberFormatter->format($entity->getCalculatedPrice()->getTotalPrice() * $this->currency->getFactor());
    }

    public function getCompatibleEntityTypes(): array
    {
        return [Product::class];
    }
}
