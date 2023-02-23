<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Domain;

use Omikron\FactFinder\Shopware6\Domain\RedirectMapping;
use PhpSpec\ObjectBehavior;
use PHPUnit\Framework\Assert;

class RedirectMappingSpec extends ObjectBehavior
{
    public function it_should_return_empty_array_as_value()
    {
        Assert::assertEquals([], (new RedirectMapping(''))->getValue());
        Assert::assertEquals([], (new RedirectMapping('abc'))->getValue());
        Assert::assertEquals([], (new RedirectMapping('abc='))->getValue());
        Assert::assertEquals([], (new RedirectMapping('abc='))->getValue());
        Assert::assertEquals([], (new RedirectMapping('=def'))->getValue());
        Assert::assertEquals([], (new RedirectMapping("abc=\n=def"))->getValue());
        Assert::assertEquals([], (new RedirectMapping('abc=some/page'))->getValue());

        Assert::assertEquals([], (new RedirectMapping(''))->getValue());
        Assert::assertEquals([], (new RedirectMapping('ab c'))->getValue());
        Assert::assertEquals([], (new RedirectMapping('ab c='))->getValue());
        Assert::assertEquals([], (new RedirectMapping('ab c='))->getValue());
        Assert::assertEquals([], (new RedirectMapping('=def'))->getValue());
        Assert::assertEquals([], (new RedirectMapping("ab c=\n=def"))->getValue());
        Assert::assertEquals([], (new RedirectMapping('abc def=some/page'))->getValue());
    }

    public function it_should_return_array_with_one_valid_item_as_value()
    {
        Assert::assertEquals(['abc' => '/some/page'], (new RedirectMapping('abc=/some/page'))->getValue());
        Assert::assertEquals(['abc' => 'http://domain.com/some/page'], (new RedirectMapping('abc=http://domain.com/some/page'))->getValue());
        Assert::assertEquals(['abc' => 'https://domain.com/some/page'], (new RedirectMapping('abc=https://domain.com/some/page'))->getValue());
        Assert::assertEquals(['abc' => 'https://domain.com/some/page?page=4&foo=bar'], (new RedirectMapping('abc=https://domain.com/some/page?page=4&foo=bar'))->getValue());

        Assert::assertEquals(['abc def' => '/some/page'], (new RedirectMapping('abc def=/some/page'))->getValue());
        Assert::assertEquals(['abc def' => 'http://domain.com/some/page'], (new RedirectMapping('abc def=http://domain.com/some/page'))->getValue());
        Assert::assertEquals(['abc def' => 'https://domain.com/some/page'], (new RedirectMapping('abc def=https://domain.com/some/page'))->getValue());
        Assert::assertEquals(['abc def' => 'https://domain.com/some/page?page=4&foo=bar'], (new RedirectMapping('abc def=https://domain.com/some/page?page=4&foo=bar'))->getValue());
    }
}
