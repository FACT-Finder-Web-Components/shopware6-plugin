<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr;

use Omikron\FactFinder\Shopware6\Config\CachedFieldRoles;
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
        CachedFieldRoles $fieldRolesService,
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
            $record = $this->convertRecord($record);
            $price  = $record['masterValues'][$priceField] ?? '';

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

    private function convertRecord(array $record): array
    {
        $record['masterValues'] = array_merge($record['masterValues'], $this->getVariant($record));

        return $record;
    }

    private function getVariant($record): array
    {
        $variantValues = $record['variantValues'] ?? [];

        if ($variantValues === []) {
            return [];
        }

        $keys      = array_keys($variantValues);
        $masterKey = array_filter($keys, fn ($key) => $variantValues[$key]['isMaster'] ?? false === 'true')[0] ?? $keys[0] ?? null;

        if ($masterKey === null) {
            return [];
        }

        return $variantValues[$masterKey] ?? [];
    }
}
