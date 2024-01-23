<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\Field\PriceCurrency;
use Omikron\FactFinder\Shopware6\Export\Formatter\NumberFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\Currency\CurrencyEntity;

class CurrencyFieldsProviderSpec extends ObjectBehavior
{
    public function let(EntityRepository $currencyRepository, ExportSettings $exportSettings): void
    {
        $exportSettings->isMultiCurrencyPriceExportEnable()->willReturn(true);
        $this->beConstructedWith($currencyRepository, $exportSettings, new NumberFormatter());
    }

    public function it_will_return_currency_list_if_no_cache_available(
        EntityRepository $currencyRepository,
        EntitySearchResult $entitySearchResult,
        ExportSettings $exportSettings
    ): void {
        $currencyRepository
            ->search(Argument::cetera(), Context::createDefaultContext())
            ->will($this->mockCurrencyRepository($entitySearchResult));

        $priceCurrency = ['test_id' => new PriceCurrency(new CurrencyEntity(), new NumberFormatter())];

        $entitySearchResult->map(Argument::cetera())->willReturn($priceCurrency);
        $this->getCurrencyFields()->shouldReturn($priceCurrency);
    }

    public function it_will_return_currency_list_from_cache(
        EntityRepository $currencyRepository,
        EntitySearchResult $entitySearchResult
    ): void {
        $currencyRepository
            ->search(Argument::cetera(), Context::createDefaultContext())
            ->will($this->mockCurrencyRepository($entitySearchResult))
            ->shouldBeCalledTimes(1);

        $priceCurrency = ['test_id' => new PriceCurrency(new CurrencyEntity(), new NumberFormatter())];
        $entitySearchResult->map(Argument::cetera())->willReturn($priceCurrency);
        $this->getCurrencyFields()->shouldReturn($priceCurrency);
        $this->getCurrencyFields()->shouldReturn($priceCurrency);
    }

    public function mockCurrencyRepository(EntitySearchResult $entitySearchResult): callable
    {
        return function () use ($entitySearchResult) {
            $currency = new CurrencyEntity();
            $currency->setId('test_id');
            $currency->setIsoCode('TST');
            $currency->setName('Test currency');

            $entitySearchResult->add($currency);

            return $entitySearchResult;
        };
    }
}
