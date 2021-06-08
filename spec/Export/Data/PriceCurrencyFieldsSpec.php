<?php

namespace spec\Omikron\FactFinder\Shopware6\Export\Data;

use Omikron\FactFinder\Shopware6\Config\ExportFilters;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\Currency\CurrencyEntity;

class PriceCurrencyFieldsSpec extends ObjectBehavior
{
    public function let(EntityRepositoryInterface $currencyRepository, ExportFilters $exportFilters)
    {
        $this->beConstructedWith($currencyRepository, $exportFilters);
    }

    public function it_will_return_currency_list_if_no_cache(EntityRepositoryInterface $currencyRepository, ExportFilters $exportFilters)
    {
        $currencyRepository
            ->search(Argument::cetera())
            ->will($this->mockCurrencyRepository());

        $this->getCurrencyFields()->shouldBeArray();
    }

    public function it_will_return_currency_list_from_cache(EntityRepositoryInterface $currencyRepository, ExportFilters $exportFilters)
    {
        $currencyRepository
            ->search(Argument::cetera())
            ->will($this->mockCurrencyRepository())
            ->shouldBeCalledTimes(1);

        $this->getCurrencyFields()->shouldBeArray();
        $this->getCurrencyFields()->shouldBeArray();
    }

    public function mockCurrencyRepository(): callable
    {
        return function () {
            $currency = new CurrencyEntity();
            $currency->setId('test_id');
            $currency->setIsoCode('TST');
            $currency->setName('Test currency');

            return new EntitySearchResult(
                '',
                1,
                new EntityCollection([$currency]),
                null,
                new Criteria(),
                new Context(new SystemSource())
            );
        };
    }
}
