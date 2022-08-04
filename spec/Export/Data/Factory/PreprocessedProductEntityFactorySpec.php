<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Data\Factory;

use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessorEntryReader;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity as ExportProductEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Factory\FactoryInterface;
use Omikron\FactFinder\Shopware6\Export\FeedPreprocessorEntry;
use Omikron\FactFinder\Shopware6\Export\FieldsProvider;
use Omikron\FactFinder\Shopware6\TestUtil\FeedPreprocessorEntryMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\ExportProductMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\ProductMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\ProductVariantMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\SalesChannelProductMockFactory;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use PHPUnit\Framework\Assert;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;

class PreprocessedProductEntityFactorySpec extends ObjectBehavior
{
    private ProductMockFactory $productMockFactory;
    private ProductVariantMockFactory $variantMockFactory;
    private FeedPreprocessorEntryMockFactory $feedPreprocessorEntryMockFactory;
    private ExportProductMockFactory $exportProductMockFactory;
    private SalesChannelProductMockFactory $salesChannelProductMockFactory;
    private Collaborator $decoratedFactory;
    private Collaborator $feedPreprocessorReader;
    private array $cachedFields = [];

    function let(
        FactoryInterface $decoratedFactory,
        FeedPreprocessorEntryReader $feedPreprocessorReader
    ) {
        $this->productMockFactory = new ProductMockFactory();
        $this->variantMockFactory = new ProductVariantMockFactory();
        $this->feedPreprocessorEntryMockFactory = new FeedPreprocessorEntryMockFactory();
        $this->exportProductMockFactory = new ExportProductMockFactory();
        $this->salesChannelProductMockFactory = new SalesChannelProductMockFactory();
        $this->decoratedFactory = $decoratedFactory;
        $this->feedPreprocessorReader = $feedPreprocessorReader;
        $this->beConstructedWith($decoratedFactory,  new FieldsProvider(new \ArrayIterator()), $feedPreprocessorReader, $this->cachedFields);
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
            $this->exportProductMockFactory->create($variants['SW100.1'], ['filterAttributes' => $filterAttributes['SW100.1']]),
            $this->exportProductMockFactory->create($variants['SW100.5'], ['filterAttributes' => $filterAttributes['SW100.5']]),
            $this->exportProductMockFactory->create($variants['SW100.7'], ['filterAttributes' => $filterAttributes['SW100.7']]),
            $this->exportProductMockFactory->create($variants['SW100.10'], ['filterAttributes' => $filterAttributes['SW100.10']]),
            $this->exportProductMockFactory->create($variants['SW100.13'], ['filterAttributes' => $filterAttributes['SW100.13']]),
            $this->exportProductMockFactory->create($variants['SW100.16'], ['filterAttributes' => $filterAttributes['SW100.16']]),
        ];
        $productEntity->setChildren(new ProductCollection(array_reduce($variants, fn(array $carriedVariants, ProductEntity $product): array => $carriedVariants + [$product->getId() => $this->salesChannelProductMockFactory->create($product)], [])));

        // Expect
        $this->feedPreprocessorReader->read($productEntity->getProductNumber())->willReturn($this->getFeedPreprocessorEntries($productEntity, [
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

    /**
     * @var ProductEntity[] $expectedVariants
     * @return FeedPreprocessorEntry[]
     */
    private function getFeedPreprocessorEntries(ProductEntity $parent, array $variantsData): array
    {
        return array_reduce(
            $variantsData,
            fn(array $acc, array $variantData): array => $acc + [$variantData['productNumber'] => $this->feedPreprocessorEntryMockFactory->create($this->variantMockFactory->create($parent, $variantData), ['filterAttributes' => $variantData['filterAttributes']])],
            []
        );
    }

    public function it_should_create_entities_with_proper_custom_fields_for_each_display_group()
    {
        // Given
        $productEntity = $this->productMockFactory->create();
        $productEntity->setConfiguratorGroupConfig(ProductMockFactory::getGroupConfigurationConfig([
            [md5('size'), 'true'],
            [md5('color'), 'false'],
            [md5('material'), 'false']
        ]));
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
        $customFieldsValues = [
            'SW100.1' => '|shape=triangle|pattern=stripes|',
            'SW100.2' => '|shape=triangle|pattern=dots|',
            'SW100.3' => '|shape=square|pattern=dots|',
            'SW100.4' => '|shape=triangle|pattern=patterned|',
            'SW100.5' => '|shape=rectangle|pattern=dots|',
            'SW100.6' => '|shape=rectangle|pattern=stripes|',
            'SW100.7' => '|shape=rectangle|pattern=dots|',
            'SW100.8' => '|shape=circle|pattern=stripes|',
        ];
        $variants = [
            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.1', 'size' => 'S', 'color' => 'green', 'material' => 'cotton', 'customFields' => $customFieldsData['SW100.1']]),
            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.2', 'size' => 'M', 'color' => 'green', 'material' => 'cotton', 'customFields' => $customFieldsData['SW100.2']]),
            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.3', 'size' => 'S', 'color' => 'green', 'material' => 'linen', 'customFields' => $customFieldsData['SW100.3']]),
            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.4', 'size' => 'M', 'color' => 'green', 'material' => 'linen', 'customFields' => $customFieldsData['SW100.4']]),
            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.5', 'size' => 'S', 'color' => 'red', 'material' => 'cotton', 'customFields' => $customFieldsData['SW100.5']]),
            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.6', 'size' => 'M', 'color' => 'red', 'material' => 'cotton', 'customFields' => $customFieldsData['SW100.6']]),
            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.7', 'size' => 'S', 'color' => 'red', 'material' => 'linen', 'customFields' => $customFieldsData['SW100.7']]),
            $this->variantMockFactory->create($productEntity, ['productNumber' => 'SW100.8', 'size' => 'M', 'color' => 'red', 'material' => 'linen', 'customFields' => $customFieldsData['SW100.8']]),
        ];
        $productEntity->setChildren(new ProductCollection(array_reduce($variants, fn(array $carriedVariants, ProductEntity $product): array => $carriedVariants + [$product->getId() => $product], [])));

        // Expect
//        $this->feedPreprocessorReader->read($productEntity->getProductNumber())->willReturn($this->getFeedPreprocessorEntries([
//            $variants['SW100.1'],
//            $variants['SW100.5'],
//            $variants['SW100.7'],
//            $variants['SW100.10'],
//            $variants['SW100.13'],
//            $variants['SW100.16'],
//        ], $variantsData));

        $entities = iterator_to_array($this->createEntities($productEntity)->getWrappedObject());

    }

}
