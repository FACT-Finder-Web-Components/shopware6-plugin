<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr;

use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\System\Currency\CurrencyFormatter;

class PriceFormatter
{
    private SalesChannelService $channelService;
    private CurrencyFormatter $currencyFormatter;
    private array $fieldRoles;

    public function __construct(
        SalesChannelService $channelService,
        CurrencyFormatter $currencyFormatter,
        array $fieldRoles
    ) {
        $this->channelService    = $channelService;
        $this->currencyFormatter = $currencyFormatter;
        $this->fieldRoles        = $fieldRoles;
    }

    public function format(array $searchResult): array
    {
        $priceField = $this->fieldRoles['price'] ?? 'Price';
        $records    = $searchResult['hits'];

        return ['records' => array_map($this->price($priceField), $records)] + $searchResult;
    }

    protected function price(string $priceField): callable
    {
        return function (array $record) use ($priceField): array {
            $price = $record['masterValues'][$priceField];
            $record['record'] = [
                    '__ORIG_PRICE__' => $price,
                    $priceField      => $this->getFormattedPrice($price)
                ] + $record['masterValues'];

            return $record;
        };
    }

    private function getFormattedPrice(float $price): string
    {
        $context = $this->channelService->getSalesChannelContext();

        return $this->currencyFormatter->formatCurrencyByLanguage(
            $price,
            $context->getCurrency()->getIsoCode(),
            $context->getLanguageId(),
            $context->getContext()
        );
    }
}
