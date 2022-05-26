<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Config;

use Omikron\FactFinder\Communication\Resource\Search;
use Omikron\FactFinder\Shopware6\Config\Communication;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class FieldRolesSpec extends ObjectBehavior
{
    public function let(Search $search, Communication $communication, SystemConfigService $systemConfigService)
    {
        $this->beConstructedWith($search, $communication, $systemConfigService);
    }

    public function it_should_map_fields_correctly(
        Search              $search,
        Communication       $communication,
        SystemConfigService $systemConfigService
    ) {

        $searchResult = [
            'fieldRoles' => [
                'brand'         => 'Manufacturer',
                'deeplink'      => 'Deeplink',
                'description'   => 'Description',
                'imageUrl'      => 'ImageUrl',
                'masterId'      => 'Master',
                'price'         => 'Price',
                'productName'   => 'Name',
                'productNumber' => 'ProductNumber',
            ]
        ];

        $communication->getChannel(Argument::any())->willReturn('some_chnnel');
        $search->search('some_chnnel', '*')->willReturn($searchResult);
        $expected =  [
            'brand'                 => 'Manufacturer',
            'campaignProductNumber' => 'ProductNumber',
            'deeplink'              => 'Deeplink',
            'description'           => 'Description',
            'displayProductNumber'  => 'ProductNumber',
            'ean'                   => null,
            'imageUrl'              => 'ImageUrl',
            'masterArticleNumber'   => 'Master',
            'price'                 => 'Price',
            'productName'           => 'Name',
            'trackingProductNumber' => 'ProductNumber'
        ];

        $this->getRoles(Argument::any())->shouldReturn($expected);
    }
}
