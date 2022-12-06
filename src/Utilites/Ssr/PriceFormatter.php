<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr;

use Omikron\FactFinder\Shopware6\Config\FieldRoles;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Shopware\Core\System\Currency\CurrencyFormatter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PriceFormatter
{
    private SalesChannelService $channelService;
    private SalesChannelContext $context;
    private CurrencyFormatter $currencyFormatter;
    private array $fieldRoles;
    private array $defaultFieldRoles;

    public function __construct(
        SalesChannelService $channelService,
        CurrencyFormatter $currencyFormatter,
        FieldRoles $fieldRolesService,
        array $fieldRoles
    ) {
        $this->channelService    = $channelService;
        $this->currencyFormatter = $currencyFormatter;
        $this->context           = $this->channelService->getSalesChannelContext();
        $this->fieldRoles        = $fieldRolesService->getRoles($this->context->getSalesChannelId());
        $this->defaultFieldRoles = $fieldRoles;
    }

    public function format(array $searchResult): array
    {
        $priceField = $this->getPriceField();
        $records    = $searchResult['hits'];

        return ['records' => array_map($this->price($priceField), $records)] + $searchResult;
    }

    protected function price(string $priceField): callable
    {
        return function (array $record) use ($priceField): array {
            $price = $record['masterValues'][$priceField] ?? '';

            if ($price === '') {
                $record['record'] = $record['masterValues'];

                return $record;
            }

            $record['record'] = [
                    '__ORIG_PRICE__' => $price,
                    $priceField      => $this->getFormattedPrice($price),
            ] + $record['masterValues'];

            return $record;
        };
    }

    private function getFormattedPrice(float $price): string
    {
        return $this->currencyFormatter->formatCurrencyByLanguage(
            $price,
            $this->context->getCurrency()->getIsoCode(),
            $this->context->getLanguageId(),
            $this->context->getContext()
        );
    }

    private function getPriceField(): string
    {
        return $this->fieldRoles['price'] ?? $this->defaultFieldRoles['price'] ?? 'Price';
    }
}
