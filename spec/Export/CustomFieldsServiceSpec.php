<?php

namespace spec\Omikron\FactFinder\Shopware6\Export;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\CustomField\CustomFieldEntity;

class CustomFieldsServiceSpec extends ObjectBehavior
{
    public function let(EntityRepository $customFieldRepository) {
        $this->beConstructedWith(
            $customFieldRepository
        );
    }

    public function it_will_return_custom_field_names_if_no_cache(
        EntityRepository $customFieldRepository
    ) {
        $customFieldRepository
           ->search(Argument::cetera())
           ->will($this->mockCustomFieldRepository());

        $this->getCustomFieldNames(['test_id'])->shouldReturn(
            ['test_id' => 'test']
        );
    }

    public function it_will_return_custom_field_from_cache(
        EntityRepository $customFieldRepository
    ) {
        $customFieldRepository
            ->search(Argument::cetera())
            ->will($this->mockCustomFieldRepository())
            ->shouldBeCalledTimes(1)
        ;

        $this->getCustomFieldNames(['test_id'])->shouldReturn(
            ['test_id' => 'test']
        );

        $this->getCustomFieldNames(['test_id'])->shouldReturn(
            ['test_id' => 'test']
        );
    }
    private function mockCustomFieldRepository(): callable
    {
        return function () {
            $customFields = new CustomFieldEntity();
            $customFields->setName('test');
            $customFields->setId('test_id');
            return new EntitySearchResult(
                '',
                1,
                new EntityCollection([$customFields]),
                null,
                new Criteria(),
                new Context(new SystemSource())
            );
        };
    }
}
