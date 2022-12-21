<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Data\Factory;

use Omikron\FactFinder\Shopware6\Config\ExportSettings;
use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessor;
use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessorEntryPersister;
use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessorEntryReader;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity as ExportProductEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Factory\FactoryInterface;
use Omikron\FactFinder\Shopware6\Export\FeedPreprocessorEntry;
use Omikron\FactFinder\Shopware6\Export\Field\CategoryPath;
use Omikron\FactFinder\Shopware6\Export\FieldsProvider;
use Omikron\FactFinder\Shopware6\Export\SalesChannelService;
use Omikron\FactFinder\Shopware6\TestUtil\ExportProductMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\FeedPreprocessorEntryMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\ProductMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\ProductVariantMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\SalesChannelProductMockFactory;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PreprocessedProductEntityFactorySpec extends ObjectBehavior
{
    private ProductMockFactory $productMockFactory;
    private ProductVariantMockFactory $variantMockFactory;
    private FeedPreprocessorEntryMockFactory $feedPreprocessorEntryMockFactory;
    private ExportProductMockFactory $exportProductMockFactory;
    private SalesChannelProductMockFactory $salesChannelProductMockFactory;
    private Collaborator $exportSettings;
    private Collaborator $decoratedFactory;
    private Collaborator $feedPreprocessorReader;
    private Collaborator $salesChannelService;
    private Collaborator $feedPreprocessor;
    private \Traversable $cachedFields;

    function let(
        SalesChannelContext $salesChannelContext,
        Context $context,
        SalesChannelService $salesChannelService,
        FactoryInterface $decoratedFactory,
        FeedPreprocessorEntryReader $feedPreprocessorReader,
        ExportSettings $exportSettings,
        FeedPreprocessorEntryPersister $entryPersister,
        FeedPreprocessor $feedPreprocessor
    ) {
        $salesChannelContext->getContext()->willReturn($context);
        $salesChannelContext->getLanguageId()->willReturn('');
        $salesChannelService->getSalesChannelContext()->willReturn($salesChannelContext);
        $this->productMockFactory = new ProductMockFactory();
        $this->variantMockFactory = new ProductVariantMockFactory();
        $this->feedPreprocessorEntryMockFactory = new FeedPreprocessorEntryMockFactory();
        $this->exportProductMockFactory = new ExportProductMockFactory();
        $this->salesChannelProductMockFactory = new SalesChannelProductMockFactory();
        $this->feedPreprocessorReader = $feedPreprocessorReader;
        $this->decoratedFactory = $decoratedFactory;
        $this->cachedFields = new \ArrayIterator();
        $exportSettings->isExportCacheEnable()->willReturn(true);
        $this->exportSettings = $exportSettings;
        $this->salesChannelService = $salesChannelService;
        $this->feedPreprocessor = $feedPreprocessor;
        $this->beConstructedWith(
            $this->decoratedFactory,
            $this->salesChannelService,
            new FieldsProvider(new \ArrayIterator()),
            $this->exportSettings,
            $this->feedPreprocessorReader,
            $entryPersister,
            $this->feedPreprocessor,
            $this->cachedFields
        );
    }

    public function it_should_handle_entities_without_cache_using_fallback_from_decorated_factory(): void
    {
        // Given
        $productEntity = $this->productMockFactory->create();

        // Expect
        $this->feedPreprocessorReader->read($productEntity->getProductNumber(), Argument::any())->willReturn([]);
        $this->feedPreprocessor->createEntries($productEntity, Argument::any())->willReturn(['fallback_data']);
        $this->decoratedFactory->createEntities($productEntity, Argument::any())->willYield(['fallback_data']);

        // When
        $this->createEntities($productEntity)->shouldYield(['fallback_data']);
    }

    public function it_should_create_entities_for_variants_that_should_be_visible(): void
    {
        // Given
        $productEntity = $this->productMockFactory->create();
        $variantsData = [
            'SW100.1' => ['productNumber' => 'SW100.1', 'size' => 'S', 'color' => 'red', 'material' => 'cotton'],
            'SW100.2' => ['productNumber' => 'SW100.2', 'size' => 'M', 'color' => 'red', 'material' => 'cotton'],
            'SW100.3' => ['productNumber' => 'SW100.3', 'size' => 'L', 'color' => 'red', 'material' => 'cotton'],
            'SW100.4' => ['productNumber' => 'SW100.4', 'size' => 'S', 'color' => 'blue', 'material' => 'cotton'],
            'SW100.5' => ['productNumber' => 'SW100.5', 'size' => 'M', 'color' => 'blue', 'material' => 'cotton'],
            'SW100.6' => ['productNumber' => 'SW100.6', 'size' => 'L', 'color' => 'blue', 'material' => 'cotton'],
            'SW100.7' => ['productNumber' => 'SW100.7', 'size' => 'S', 'color' => 'green', 'material' => 'cotton'],
            'SW100.8' => ['productNumber' => 'SW100.8', 'size' => 'M', 'color' => 'green', 'material' => 'cotton'],
            'SW100.9' => ['productNumber' => 'SW100.9', 'size' => 'L', 'color' => 'green', 'material' => 'cotton'],
            'SW100.10' => ['productNumber' => 'SW100.10', 'size' => 'S', 'color' => 'red', 'material' => 'linen'],
            'SW100.11' => ['productNumber' => 'SW100.11', 'size' => 'M', 'color' => 'red', 'material' => 'linen'],
            'SW100.12' => ['productNumber' => 'SW100.12', 'size' => 'L', 'color' => 'red', 'material' => 'linen'],
            'SW100.13' => ['productNumber' => 'SW100.13', 'size' => 'S', 'color' => 'blue', 'material' => 'linen'],
            'SW100.14' => ['productNumber' => 'SW100.14', 'size' => 'M', 'color' => 'blue', 'material' => 'linen'],
            'SW100.15' => ['productNumber' => 'SW100.15', 'size' => 'L', 'color' => 'blue', 'material' => 'linen'],
            'SW100.16' => ['productNumber' => 'SW100.16', 'size' => 'S', 'color' => 'green', 'material' => 'linen'],
            'SW100.17' => ['productNumber' => 'SW100.17', 'size' => 'M', 'color' => 'green', 'material' => 'linen'],
            'SW100.18' => ['productNumber' => 'SW100.18', 'size' => 'L', 'color' => 'green', 'material' => 'linen'],
        ];
        $variants = array_map(fn (array $variantData) => $this->variantMockFactory->create($productEntity, $variantData), $variantsData);
        $filterAttributes = [
            'SW100.1' => 'size=S|size=M|size=L|color=red|material=cotton',
            'SW100.5' => 'size=S|size=M|size=L|color=blue|material=cotton',
            'SW100.7' => 'size=S|size=M|size=L|color=green|material=cotton',
            'SW100.10' => 'size=S|size=M|size=L|color=red|material=linen',
            'SW100.13' => 'size=S|size=M|size=L|color=blue|material=linen',
            'SW100.16' => 'size=S|size=M|size=L|color=green|material=linen',
        ];
        $expectedVariants = [
            $this->exportProductMockFactory->create($variants['SW100.1'], ['filterAttributes' => $filterAttributes['SW100.1'], 'parent' => $productEntity]),
            $this->exportProductMockFactory->create($variants['SW100.5'], ['filterAttributes' => $filterAttributes['SW100.5'], 'parent' => $productEntity]),
            $this->exportProductMockFactory->create($variants['SW100.7'], ['filterAttributes' => $filterAttributes['SW100.7'], 'parent' => $productEntity]),
            $this->exportProductMockFactory->create($variants['SW100.10'], ['filterAttributes' => $filterAttributes['SW100.10'], 'parent' => $productEntity]),
            $this->exportProductMockFactory->create($variants['SW100.13'], ['filterAttributes' => $filterAttributes['SW100.13'], 'parent' => $productEntity]),
            $this->exportProductMockFactory->create($variants['SW100.16'], ['filterAttributes' => $filterAttributes['SW100.16'], 'parent' => $productEntity]),
            $this->exportProductMockFactory->create($productEntity),
        ];
        $productEntity->setChildren($this->getProductCollection($variants));

        // Expect
        $this->feedPreprocessorReader->read($productEntity->getProductNumber(), Argument::any())->willReturn($this->getFeedPreprocessorEntries($productEntity, [
            $variantsData['SW100.1'] + ['filterAttributes' => $filterAttributes['SW100.1']],
            $variantsData['SW100.5'] + ['filterAttributes' => $filterAttributes['SW100.5']],
            $variantsData['SW100.7'] + ['filterAttributes' => $filterAttributes['SW100.7']],
            $variantsData['SW100.10'] + ['filterAttributes' => $filterAttributes['SW100.10']],
            $variantsData['SW100.13'] + ['filterAttributes' => $filterAttributes['SW100.13']],
            $variantsData['SW100.16'] + ['filterAttributes' => $filterAttributes['SW100.16']],
        ]));

        // When
        $exportedProducts = iterator_to_array($this->createEntities($productEntity)->getWrappedObject());

        // Then
        Assert::assertEquals(
            array_values(array_map(fn(ExportProductEntity $variant) => $variant->toArray(), $expectedVariants)),
            array_values(array_map(fn (ExportProductEntity $product) => $product->toArray(), $exportedProducts))
        );
    }

    public function it_should_create_entities_with_cached_category_path(
        CategoryPath $categoryPathField,
        FieldsProvider $fieldsProvider,
        FeedPreprocessorEntryPersister $entryPersister
    ) {
        // Given
        $categoryPathName = 'CategoryPath';
        $categoryPathValue = 'Sports/Books%2C+Clothing+%26+Games/Movies%2C+Electronics+%26+Clothing';
        $this->beConstructedWith(
            $this->decoratedFactory,
            $this->salesChannelService,
            $fieldsProvider,
            $this->exportSettings,
            $this->feedPreprocessorReader,
            $entryPersister,
            $this->feedPreprocessor,
            $this->cachedFields
        );

        $productEntity = $this->productMockFactory->create();
        $variantsData = [
            'SW100.1' => ['productNumber' => 'SW100.1', 'size' => 'S', 'color' => 'red', 'material' => 'cotton'],
            'SW100.2' => ['productNumber' => 'SW100.2', 'size' => 'M', 'color' => 'blue', 'material' => 'cotton'],
        ];
        $variants = array_map(fn (array $variantData) => $this->variantMockFactory->create($productEntity, $variantData), $variantsData);
        $filterAttributes = [
            'SW100.1' => 'size=S|size=M|color=red|material=cotton',
            'SW100.2' => 'size=S|size=M|color=blue|material=cotton',
        ];
        $expectedVariants = array_map(function(array $expectedVariantData) use ($filterAttributes, $categoryPathField, $variants, $categoryPathName, $categoryPathValue, $productEntity): ExportProductEntity {
            $productNumber = $expectedVariantData['productNumber'];
            $product = $variants[$productNumber];
            $categoryPathField->getName()->willReturn($categoryPathName);
            $categoryPathField->getValue($product)->willReturn($categoryPathValue);

            return $this->exportProductMockFactory->create(
                $product,
                [
                    'filterAttributes' => $filterAttributes[$productNumber],
                    'additionalCache' => [$categoryPathName => $categoryPathValue],
                    'productFields' => [$categoryPathField->getWrappedObject()],
                    'parent' => $productEntity
                ]
            );
        }, $variantsData);
        $productEntity->setChildren($this->getProductCollection($variants));

        foreach ($variantsData as $variantData) {
            $productNumber = $variantData['productNumber'];
            $product = $variants[$productNumber];
            $categoryPathField->getName()->willReturn($categoryPathName);
            $fieldsProvider->getFields(SalesChannelProductEntity::class)->willReturn([$categoryPathField]);
        }

        // Expect
        $this->feedPreprocessorReader->read($productEntity->getProductNumber(), Argument::any())->willReturn(
            $this->getFeedPreprocessorEntries(
                $productEntity,
                [
                    $variantsData['SW100.1'] + ['filterAttributes' => $filterAttributes['SW100.1'], 'additionalCache' => ['CategoryPath' => 'Sports/Books%2C+Clothing+%26+Games/Movies%2C+Electronics+%26+Clothing']],
                    $variantsData['SW100.2'] + ['filterAttributes' => $filterAttributes['SW100.2'], 'additionalCache' => ['CategoryPath' => 'Sports/Books%2C+Clothing+%26+Games/Movies%2C+Electronics+%26+Clothing']],
                ]
            )
        );

        // When
        $exportedProducts = iterator_to_array($this->createEntities($productEntity)->getWrappedObject());

        // Then
        Assert::assertEquals(
            array_values(array_map(fn(ExportProductEntity $variant) => $variant->toArray(), $expectedVariants)),
            array_values(array_map(fn (ExportProductEntity $product) => $product->toArray(), $exportedProducts))
        );
    }

    public function it_should_create_entities_with_proper_custom_fields_for_each_display_group()
    {
        $this->validateExportedCustomFields(
            [
                [md5('size'), 'true'],
                [md5('color'), 'false'],
                [md5('material'), 'false']
            ],
            [
                'SW100.1' => [
                    'filterAttributes' => 'material=cotton|material=linen|color=red|color=green|size=S',
                    'customFields' => '|shape=rectangle|pattern=dots|shape=square|shape=triangle|pattern=stripes|',
                ],
                'SW100.8' => [
                    'filterAttributes' => 'material=cotton|material=linen|color=red|color=green|size=M',
                    'customFields' => '|shape=triangle|pattern=patterned|pattern=dots|shape=rectangle|pattern=stripes|shape=circle|',
                ],
            ],
            [
                '|pattern=dots|pattern=stripes|shape=rectangle|shape=square|shape=triangle|',
                '|pattern=dots|pattern=patterned|pattern=stripes|shape=circle|shape=rectangle|shape=triangle|',
            ]
        );
        $this->validateExportedCustomFields(
            [
                [md5('size'), 'false'],
                [md5('color'), 'true'],
                [md5('material'), 'false']
            ],
            [
                'SW100.1' => [
                    'filterAttributes' => 'size=S|size=M|material=cotton|material=linen|color=green',
                    'customFields' => '|shape=triangle|pattern=patterned|pattern=dots|shape=square|pattern=stripes|'
                ],
                'SW100.8' => [
                    'filterAttributes' => 'size=S|size=M|material=cotton|material=linen|color=red',
                    'customFields' => '|shape=rectangle|pattern=dots|pattern=stripes|shape=circle|'
                ],
            ],
            [
                '|pattern=dots|pattern=patterned|pattern=stripes|shape=square|shape=triangle|',
                '|pattern=dots|pattern=stripes|shape=circle|shape=rectangle|',
            ]
        );
        $this->validateExportedCustomFields(
            [
                [md5('size'), 'false'],
                [md5('color'), 'true'],
                [md5('material'), 'true']
            ],
            [
                'SW100.1' => [
                    'filterAttributes' => 'size=S|size=M|material=cotton|color=green',
                    'customFields' => '|shape=triangle|pattern=dots|pattern=stripes|',
                ],
                'SW100.3' => [
                    'filterAttributes' => 'size=S|size=M|color=green|material=linen',
                    'customFields' => '|shape=triangle|pattern=patterned|shape=square|pattern=dots|',
                ],
                'SW100.6' => [
                    'filterAttributes' => 'size=S|size=M|material=cotton|color=red',
                    'customFields' => '|shape=rectangle|pattern=dots|pattern=stripes|',
                ],
                'SW100.8' => [
                    'filterAttributes' => 'size=S|size=M|color=red|material=linen',
                    'customFields' => '|shape=rectangle|pattern=dots|shape=circle|pattern=stripes|',
                ],
            ],
            [
                '|pattern=dots|pattern=stripes|shape=triangle|',
                '|pattern=dots|pattern=patterned|shape=square|shape=triangle|',
                '|pattern=dots|pattern=stripes|shape=rectangle|',
                '|pattern=dots|pattern=stripes|shape=circle|shape=rectangle|',
            ]
        );
        $this->validateExportedCustomFields(
            [
                [md5('size'), 'true'],
                [md5('color'), 'true'],
                [md5('material'), 'true']
            ],
            [
                'SW100.1' => [
                    'filterAttributes' => '|size=S|material=cotton|color=green',
                    'customFields' => '|shape=triangle|pattern=stripes|',
                ],
                'SW100.2' => [
                    'filterAttributes' => '|material=cotton|color=green|size=M',
                    'customFields' => '|shape=triangle|pattern=dots|',
                ],
                'SW100.3' => [
                    'filterAttributes' => '|size=S|color=green|material=linen',
                    'customFields' => '|shape=square|pattern=dots|',
                ],
                'SW100.4' => [
                    'filterAttributes' => '|color=green|size=M|material=linen',
                    'customFields' => '|shape=triangle|pattern=patterned|',
                ],
                'SW100.5' => [
                    'filterAttributes' => '|size=S|material=cotton|color=red',
                    'customFields' => '|shape=rectangle|pattern=dots|',
                ],
                'SW100.6' => [
                    'filterAttributes' => '|material=cotton|color=red|size=M',
                    'customFields' => '|shape=rectangle|pattern=stripes|',
                ],
                'SW100.7' => [
                    'filterAttributes' => '|size=S|color=red|material=linen',
                    'customFields' => '|shape=rectangle|pattern=dots|',
                ],
                'SW100.8' => [
                    'filterAttributes' => '|color=red|size=M|material=linen',
                    'customFields' => '|shape=circle|pattern=stripes|',
                ],
            ],
            [
                '|pattern=stripes|shape=triangle|',
                '|pattern=dots|shape=triangle|',
                '|pattern=dots|shape=square|',
                '|pattern=patterned|shape=triangle|',
                '|pattern=dots|shape=rectangle|',
                '|pattern=stripes|shape=rectangle|',
                '|pattern=dots|shape=rectangle|',
                '|pattern=stripes|shape=circle|',
            ]
        );
    }

    private function validateExportedCustomFields(
        array $groupConfigurationConfig,
        array $feedPreprocessorReaderData,
        array $expectedValues
    ): void {
        // Given
        $productEntity = $this->productMockFactory->create();
        $productEntity->setConfiguratorGroupConfig(ProductMockFactory::getGroupConfigurationConfig($groupConfigurationConfig));
        $customFieldsData = [
            'SW100.1' => ['shape' => 'triangle', 'pattern' => 'stripes'],
            'SW100.2' => ['shape' => 'triangle', 'pattern' => 'dots'],
            'SW100.3' => ['shape' => 'square', 'pattern' => 'dots'],
            'SW100.4' => ['shape' => 'triangle', 'pattern' => 'patterned'],
            'SW100.5' => ['shape' => 'rectangle', 'pattern' => 'dots'],
            'SW100.6' => ['shape' => 'rectangle', 'pattern' => 'stripes'],
            'SW100.7' => ['shape' => 'rectangle', 'pattern' => 'dots'],
            'SW100.8' => ['shape' => 'circle', 'pattern' => 'stripes'],
        ];
        $variantsData = [
            'SW100.1' => ['productNumber' => 'SW100.1', 'size' => 'S', 'color' => 'green', 'material' => 'cotton'],
            'SW100.2' => ['productNumber' => 'SW100.2', 'size' => 'M', 'color' => 'green', 'material' => 'cotton'],
            'SW100.3' => ['productNumber' => 'SW100.3', 'size' => 'S', 'color' => 'green', 'material' => 'linen'],
            'SW100.4' => ['productNumber' => 'SW100.4', 'size' => 'M', 'color' => 'green', 'material' => 'linen'],
            'SW100.5' => ['productNumber' => 'SW100.5', 'size' => 'S', 'color' => 'red', 'material' => 'cotton'],
            'SW100.6' => ['productNumber' => 'SW100.6', 'size' => 'M', 'color' => 'red', 'material' => 'cotton'],
            'SW100.7' => ['productNumber' => 'SW100.7', 'size' => 'S', 'color' => 'red', 'material' => 'linen'],
            'SW100.8' => ['productNumber' => 'SW100.8', 'size' => 'M', 'color' => 'red', 'material' => 'linen'],
        ];
        $variants = array_map(fn (array $variantData) => $this->variantMockFactory->create($productEntity, $variantData), $variantsData);
        array_map(fn (ProductEntity $variant) => $variant->setCustomFields($customFieldsData[$variant->getProductNumber()]), $variants);
        $productEntity->setChildren($this->getProductCollection($variants));

        // Expect
        $this->feedPreprocessorReader->read($productEntity->getProductNumber(), Argument::any())->willReturn(
            $this->getFeedPreprocessorEntries(
                $productEntity,
                array_map(fn(string $productNumber, array $data) => $variantsData[$productNumber] + $feedPreprocessorReaderData[$productNumber], array_keys($feedPreprocessorReaderData), array_values($feedPreprocessorReaderData))
            )
        );

        /** @var ExportProductEntity[] $entities */
        $entities = iterator_to_array($this->createEntities($productEntity)->getWrappedObject());

        foreach ($expectedValues as $key => $expectedValue) {
            $gluedCustomFields = explode('|', trim($entities[$key]->getCustomFields(), '|'));
            sort($gluedCustomFields);
            $gluedCustomFields = sprintf('|%s|', implode('|', $gluedCustomFields));
            Assert::assertEquals($expectedValue, $gluedCustomFields);
        }
    }

    /**
     * @var ProductEntity[] $expectedVariants
     * @return FeedPreprocessorEntry[]
     */
    private function getFeedPreprocessorEntries(ProductEntity $parent, array $variantsData): array
    {
        return array_reduce(
            $variantsData,
            fn(array $acc, array $variantData): array => $acc + [
                $variantData['productNumber'] => $this->feedPreprocessorEntryMockFactory->create(
                    $this->variantMockFactory->create($parent, $variantData),
                    [
                        'filterAttributes' => $variantData['filterAttributes'],
                        'customFields' => $variantData['customFields'] ?? '',
                        'additionalCache' => $variantData['additionalCache'] ?? []
                    ]
                )
            ],
            []
        );
    }

    /**
     * @param ProductEntity[] $variants
     */
    private function getProductCollection(array $variants): ProductCollection
    {
        return new ProductCollection(
            array_reduce(
                $variants,
                fn(array $carriedVariants, ProductEntity $product): array =>
                    $carriedVariants + [$product->getId() => $this->salesChannelProductMockFactory->create($product)],
                []
            ));
    }
}
