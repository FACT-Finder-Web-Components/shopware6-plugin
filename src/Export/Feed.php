<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Export\Data\DataProviderInterface;
use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;
use Omikron\FactFinder\Shopware6\Export\Stream\StreamInterface;
use Shopware\Core\System\Currency\CurrencyEntity;

class Feed
{
    /** @var DataProviderInterface */
    private $dataProvider;

    /** @var FilterInterface */
    private $filter;

    /** @var array */
    private $currencyList;

    /** @var CurrencyEntity */
    private $defaultCurrency;

    public function __construct(DataProviderInterface $dataProvider, FilterInterface $filter)
    {
        $this->dataProvider = $dataProvider;
        $this->filter       = $filter;
    }

    public function setCurrencyList(array $currencyList): self
    {
        $this->currencyList = $currencyList;

        return $this;
    }

    public function setDefaultCurrency(CurrencyEntity $defaultCurrency): self
    {
        $this->defaultCurrency = $defaultCurrency;

        return $this;
    }

    public function getCurrencyList(): array
    {
        return $this->currencyList;
    }

    public function getDefaultCurrency(): CurrencyEntity
    {
        return $this->defaultCurrency;
    }

    public function generate(StreamInterface $stream, array $columns): void
    {
        $stream->addEntity($columns);
        $emptyRecord = array_combine($columns, array_fill(0, count($columns), ''));
        foreach ($this->dataProvider->getEntities() as $entity) {
            $entityData = array_merge($emptyRecord, array_intersect_key($entity->toArray(), $emptyRecord));
            $entityData = $this->calculatePrice($entityData);
            $stream->addEntity($this->prepare($entityData));
        }
    }

    private function calculatePrice(array $entityData): array
    {
        $defaultCurrencyPrice = $entityData['Price'];
        foreach ($this->getCurrencyList() as $currency) {
            if ($currency['factor'] != $this->getDefaultCurrency()->getFactor()) {
                $entityData['Price_' . $currency['iso_code']] = round($defaultCurrencyPrice * $currency['factor'], 2);
            }
        }

        return $entityData;
    }

    private function prepare(array $data): array
    {
        return array_map([$this->filter, 'filterValue'], $data);
    }
}
