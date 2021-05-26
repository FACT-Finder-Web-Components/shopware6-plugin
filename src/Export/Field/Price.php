<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Export\Formatter\NumberFormatter;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\Content\Product\SalesChannel\Price\ProductPriceCalculator;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;

class Price implements FieldInterface
{
    /** @var NumberFormatter */
    private $numberFormatter;

    /** @var ProductPriceCalculator */
    private $priceCalculator;

    /** @var SalesChannelService */
    private $channelService;

    public function __construct(
        NumberFormatter $numberFormatter,
        ProductPriceCalculator $priceCalculator,
        SalesChannelService $channelService
    ) {
        $this->numberFormatter = $numberFormatter;
        $this->priceCalculator = $priceCalculator;
        $this->channelService  = $channelService;
    }

    public function getName(): string
    {
        return 'Price';
    }

    public function getValue(Product $product): string
    {
        $this->priceCalculator->calculate([$product], $this->channelService->getSalesChannelContext());
        if ($product->getCalculatedPrices()->count() === 0) {
            return $this->numberFormatter->format((float)$product->getCalculatedPrice()->getTotalPrice());
        }
        return $this->numberFormatter->format((float)$product->getCalculatedPrices()->first()->getUnitPrice());
    }
}
