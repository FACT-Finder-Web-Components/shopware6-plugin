<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Field;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use PhpSpec\ObjectBehavior;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionCollection;

class FilterAttributesSpec extends ObjectBehavior
{
    public function let(PropertyFormatter $propertyFormatter, ExportSettings $exportSettings)
    {
        $exportSettings->getDisabledPropertyGroups()->willReturn([]);
        $this->beConstructedWith($propertyFormatter, $exportSettings);
    }

    public function it_does_not_fail_if_product_have_no_properties(
        Product $product,
        ProductCollection $productCollection,
        PropertyGroupOptionCollection $emptyProperties
    ) {
        $product->getChildren()->willReturn($productCollection);
        $emptyProperties->getElements()->willReturn([]);
        $product->getProperties()->willReturn($emptyProperties);
        $this->shouldNotThrow()->during('getValue', [$product]);
    }
}
