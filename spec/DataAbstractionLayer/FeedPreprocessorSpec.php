<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\DataAbstractionLayer;

use Omikron\FactFinder\Shopware6\Export\Field\CustomFields as ExportCustomFields;
use Omikron\FactFinder\Shopware6\Export\Filter\TextFilter;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Omikron\FactFinder\Shopware6\TestUtil\ProductMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\ProductVariantMockFactory;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Subject;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Shopware\Core\Content\Product\DataAbstractionLayer\VariantListingConfig;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;
use PhpSpec\Wrapper\Collaborator;

class FeedPreprocessorSpec extends ObjectBehavior
{
    private ProductMockFactory $productMockFactory;
    private ProductVariantMockFactory $variantMockFactory;
    private Collaborator $customFields;
    private Context $context;

    function let(EventDispatcherInterface $eventDispatcher, ExportCustomFields $customFields)
    {
        $eventDispatcher->dispatch(Argument::any())->willReturn(new Event());
        $this->beConstructedWith(new PropertyFormatter(new TextFilter()), $eventDispatcher, $customFields);
        $this->context = new Context(new SystemSource());
        $this->productMockFactory = new ProductMockFactory();
        $this->variantMockFactory = new ProductVariantMockFactory();
        $this->customFields = $customFields;
    }

//    function it_should_create_only_entries_for_variants_that_should_be_visible()
//    {
//        $productEntity = $this->productMockFactory->create();
//        $variants      = [
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.1', 'size' => 'S', 'color' => 'red', 'material' => 'cotton']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.2', 'size' => 'M', 'color' => 'red', 'material' => 'cotton']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.3', 'size' => 'L', 'color' => 'red', 'material' => 'cotton']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.4', 'size' => 'S', 'color' => 'blue', 'material' => 'cotton']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.5', 'size' => 'M', 'color' => 'blue', 'material' => 'cotton']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.6', 'size' => 'L', 'color' => 'blue', 'material' => 'cotton']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.7', 'size' => 'S', 'color' => 'green', 'material' => 'cotton']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.8', 'size' => 'M', 'color' => 'green', 'material' => 'cotton']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.9', 'size' => 'L', 'color' => 'green', 'material' => 'cotton']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.10', 'size' => 'S', 'color' => 'red', 'material' => 'linen']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.11', 'size' => 'M', 'color' => 'red', 'material' => 'linen']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.12', 'size' => 'L', 'color' => 'red', 'material' => 'linen']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.13', 'size' => 'S', 'color' => 'blue', 'material' => 'linen']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.14', 'size' => 'M', 'color' => 'blue', 'material' => 'linen']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.15', 'size' => 'L', 'color' => 'blue', 'material' => 'linen']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.16', 'size' => 'S', 'color' => 'green', 'material' => 'linen']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.17', 'size' => 'M', 'color' => 'green', 'material' => 'linen']),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.18', 'size' => 'L', 'color' => 'green', 'material' => 'linen']),
//        ];
//        foreach ($variants as $variant) {
//            $this->customFields->getValue($variant)->willReturn('');
//        }
//
//        $productEntity->setChildren(
//            new ProductCollection(
//                array_reduce($variants, fn(array $carriedVariants, ProductEntity $product): array
//                => $carriedVariants + [$product->getId() => $product], [])));
//
//        /** @var Subject $entries */
//        $entries = $this->createEntries($productEntity, $this->context);
//
//        $entries->shouldBeArray();
//        //3 colors * 2 materials = 6 combinations
//        $entries->shouldHaveCount(1); //invalid
//
//        $this->validateFilterAttributes(
//            $entries->getWrappedObject(),
//            [
//                'size=S|size=M|size=L|color=red|material=cotton',
//                'size=S|size=M|size=L|color=blue|material=cotton',
//                'size=S|size=M|size=L|color=green|material=cotton',
//                'size=S|size=M|size=L|color=red|material=linen',
//                'size=S|size=M|size=L|color=blue|material=linen',
//                'size=S|size=M|size=L|color=green|material=linen',
//            ]
//        );
//    }
//
//    function it_should_create_entries_with_empty_string_in_custom_fields_when_custom_fields_not_set()
//    {
//        // Given
//        $productEntity = $this->productMockFactory->create();
//        $productEntity->setVariantListingConfig(new VariantListingConfig(null, 'SW100', null));
//        $variant = $this->variantMockFactory->create(
//            $productEntity,
//            ['productNumber' => 'SW100.1', 'size' => 'S', 'color' => 'red', 'material' => 'cotton', 'customFields' => '']
//        );
//        $productEntity->setChildren(new ProductCollection([$variant]));
//
//        // Expect
//        $this->customFields->getValue($variant)->willReturn('');
//
//        // When
//        $entries = $this->createEntries($productEntity, $this->context);
//        $entries->shouldBeArray();
//        $entries->shouldHaveCount(1);
//
//        // Then
//        Assert::assertEquals('', array_values($entries->getWrappedObject())[0]['customFields']);
//    }
//
////    function it_should_create_entries_with_proper_custom_fields_for_each_display_group()
////    {
////        $this->validateCustomFields(
////            [
////                [md5('size'), 'true'],
////                [md5('color'), 'false'],
////                [md5('material'), 'false']
////            ],
////            [
////                '|pattern=dots|pattern=patterned|pattern=stripes|shape=circle|shape=rectangle|shape=square|shape=triangle|',
////                '|pattern=dots|pattern=patterned|pattern=stripes|shape=circle|shape=rectangle|shape=triangle|',
////            ]
////        );
////        $this->validateCustomFields(
////            [
////                [md5('size'), 'false'],
////                [md5('color'), 'true'],
////                [md5('material'), 'false']
////            ],
////            [
////                '|pattern=dots|pattern=patterned|pattern=stripes|shape=circle|shape=rectangle|shape=square|shape=triangle|',
////                '|pattern=dots|pattern=stripes|shape=circle|shape=rectangle|',
////            ]
////        );
////        $this->validateCustomFields(
////            [
////                [md5('size'), 'false'],
////                [md5('color'), 'true'],
////                [md5('material'), 'true']
////            ],
////            [
////                '|pattern=dots|pattern=stripes|shape=triangle|',
////                '|pattern=dots|pattern=patterned|shape=square|shape=triangle|',
////                '|pattern=dots|pattern=stripes|shape=rectangle|',
////                '|pattern=dots|pattern=stripes|shape=circle|shape=rectangle|',
////            ]
////        );
////        $this->validateCustomFields(
////            [
////                [md5('size'), 'true'],
////                [md5('color'), 'true'],
////                [md5('material'), 'true']
////            ],
////            [
////                '|pattern=stripes|shape=triangle|',
////                '|pattern=dots|shape=triangle|',
////                '|pattern=dots|shape=square|',
////                '|pattern=patterned|shape=triangle|',
////                '|pattern=dots|shape=rectangle|',
////                '|pattern=stripes|shape=rectangle|',
////                '|pattern=dots|shape=rectangle|',
////                '|pattern=stripes|shape=circle|',
////            ]
////        );
////    }
//
//    function it_should_export_only_one_entry_if_main_variant_is_selected()
//    {
//        $productEntity = $this->productMockFactory->create();
//        $productEntity->setVariantListingConfig(new VariantListingConfig(null, 'SW100', null));
//        $customFieldsData = [
//            'SW100.1' => ['shape' => 'triangle', 'pattern' => 'stripes'],
//            'SW100.2' => ['shape' => 'rectangle', 'pattern' => 'patterned'],
//            'SW100.3' => ['shape' => 'square', 'pattern' => 'dots'],
//        ];
//
//        $customFieldsValues = [
//            'SW100.1' => '|shape=triangle|pattern=stripes|',
//            'SW100.2' => '|shape=rectangle|pattern=patterned|',
//            'SW100.3' => '|shape=square|pattern=dots|',
//        ];
//
//        $variants = [
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.1', 'size' => 'S', 'color' => 'red', 'material' => 'cotton', 'customFields' => $customFieldsData['SW100.1']]),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.2', 'size' => 'M', 'color' => 'red', 'material' => 'linen', 'customFields' => $customFieldsData['SW100.1']]),
//            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.3', 'size' => 'L', 'color' => 'red', 'material' => 'wool', 'customFields' => $customFieldsData['SW100.1']])
//        ];
//
//        foreach ($variants as $variant) {
//            $this->customFields->getValue($variant)->willReturn($customFieldsValues[$variant->getProductNumber()]);
//        }
//
//        $productEntity->setChildren(
//            new ProductCollection(
//                array_reduce($variants, fn(array $carriedVariants, ProductEntity $product): array
//                => $carriedVariants + [$product->getId() => $product], [])));
//
//        /** @var Subject $entries */
//        $entries = $this->createEntries($productEntity, $this->context);
//        $entries->shouldBeArray();
//        //only one entry should be producted
//        $entries->shouldHaveCount(1);
//
//        $this->validateFilterAttributes($entries->getWrappedObject(), [
//            'size=S|size=M|size=L|color=red|material=cotton|material=linen|material=wool'
//        ]);
//
//
//    }
//
//    private function validateFilterAttributes(array $entries, array $filters)
//    {
//        var_dump($entries); die();
//        $expected = count($filters);
//        $isContainingFilter = fn(string $filter): callable
//            => fn(array $entry): bool
//                => strpos($entry['filterAttributes'], $filter) !== false;
//
//        $match = array_reduce($filters, fn (int $score, string $filter)
//            => $score + (int) array_filter($entries, $isContainingFilter($filter)), 0);
//
//        Assert::assertEquals($expected, $match);
//    }
//
//    private function validateCustomFields(array $groupConfigurationConfig, array $expectedValues)
//    {
//        // Given
//        $productMockFactory = new ProductMockFactory();
//        $variantMockFactory = new ProductVariantMockFactory();
//        $productEntity = $productMockFactory->create();
//        $productEntity->setVariantListingConfig(
//            new VariantListingConfig(
//                null,
//                'SW100',
//                ProductMockFactory::getGroupConfigurationConfig($groupConfigurationConfig)
//            )
//        );
//
//        $customFieldsData = [
//            'SW100.1' => ['shape' => 'triangle', 'pattern' => 'stripes'],
//            'SW100.2' => ['shape' => 'triangle', 'pattern' => 'dots'],
//            'SW100.3' => ['shape' => 'square', 'pattern' => 'dots'],
//            'SW100.4' => ['shape' => 'triangle', 'pattern' => 'patterned'],
//            'SW100.5' => ['shape' => 'rectangle', 'pattern' => 'dots'],
//            'SW100.6' => ['shape' => 'rectangle', 'pattern' => 'stripes'],
//            'SW100.7' => ['shape' => 'rectangle', 'pattern' => 'dots'],
//            'SW100.8' => ['shape' => 'circle', 'pattern' => 'stripes'],
//        ];
//        $customFieldsValues = [
//            'SW100.1' => '|shape=triangle|pattern=stripes|',
//            'SW100.2' => '|shape=triangle|pattern=dots|',
//            'SW100.3' => '|shape=square|pattern=dots|',
//            'SW100.4' => '|shape=triangle|pattern=patterned|',
//            'SW100.5' => '|shape=rectangle|pattern=dots|',
//            'SW100.6' => '|shape=rectangle|pattern=stripes|',
//            'SW100.7' => '|shape=rectangle|pattern=dots|',
//            'SW100.8' => '|shape=circle|pattern=stripes|',
//        ];
//        $variants = [
//            $variantMockFactory->create($productEntity, ['productNumber' => 'SW100.1', 'size' => 'S', 'color' => 'green', 'material' => 'cotton', 'customFields' => $customFieldsData['SW100.1']]),
//            $variantMockFactory->create($productEntity, ['productNumber' => 'SW100.2', 'size' => 'M', 'color' => 'green', 'material' => 'cotton', 'customFields' => $customFieldsData['SW100.2']]),
//            $variantMockFactory->create($productEntity, ['productNumber' => 'SW100.3', 'size' => 'S', 'color' => 'green', 'material' => 'linen', 'customFields' => $customFieldsData['SW100.3']]),
//            $variantMockFactory->create($productEntity, ['productNumber' => 'SW100.4', 'size' => 'M', 'color' => 'green', 'material' => 'linen', 'customFields' => $customFieldsData['SW100.4']]),
//            $variantMockFactory->create($productEntity, ['productNumber' => 'SW100.5', 'size' => 'S', 'color' => 'red', 'material' => 'cotton', 'customFields' => $customFieldsData['SW100.5']]),
//            $variantMockFactory->create($productEntity, ['productNumber' => 'SW100.6', 'size' => 'M', 'color' => 'red', 'material' => 'cotton', 'customFields' => $customFieldsData['SW100.6']]),
//            $variantMockFactory->create($productEntity, ['productNumber' => 'SW100.7', 'size' => 'S', 'color' => 'red', 'material' => 'linen', 'customFields' => $customFieldsData['SW100.7']]),
//            $variantMockFactory->create($productEntity, ['productNumber' => 'SW100.8', 'size' => 'M', 'color' => 'red', 'material' => 'linen', 'customFields' => $customFieldsData['SW100.8']]),
//        ];
//        $productEntity->setChildren(new ProductCollection(array_reduce($variants, fn(array $carriedVariants, ProductEntity $product): array => $carriedVariants + [$product->getId() => $product], [])));
//
//        // Expected
//        foreach ($variants as $variant) {
//            $this->customFields->getValue($variant)->willReturn($customFieldsValues[$variant->getProductNumber()]);
//        }
//
//        // When
//        $entries = $this->createEntries($productEntity, $this->context);
//
//        // Then
//        $entries = array_values($entries->getWrappedObject());
//
//        foreach ($expectedValues as $key => $expectedValue) {
//            $gluedCustomFields = explode('|', trim($entries[$key]['customFields'], '|'));
//            sort($gluedCustomFields);
//            $gluedCustomFields = sprintf('|%s|', implode('|', $gluedCustomFields));
//            Assert::assertEquals($expectedValue, $gluedCustomFields);
//        }
//    }
}
