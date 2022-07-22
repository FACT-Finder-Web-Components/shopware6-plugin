<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Export\Data\Factory;

use Omikron\FactFinder\Shopware6\DataAbstractionLayer\FeedPreprocessorEntryReader;
use Omikron\FactFinder\Shopware6\Export\Data\Entity\ProductEntity as ExportProductEntity;
use Omikron\FactFinder\Shopware6\Export\Data\Factory\FactoryInterface;
use Omikron\FactFinder\Shopware6\Export\FeedPreprocessorEntry;
use Omikron\FactFinder\Shopware6\TestUtil\FeedPreprocessorEntryMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\ExportProductMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\ProductMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\ProductVariantMockFactory;
use PhpSpec\ObjectBehavior;
use PHPUnit\Framework\Assert;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;

class PreprocessedProductEntityFactorySpec extends ObjectBehavior
{
    private ProductMockFactory $productMockFactory;
    private ProductVariantMockFactory $variantMockFactory;
    private FeedPreprocessorEntryMockFactory $feedPreprocessorEntryMockFactory;
    private ExportProductMockFactory $exportProductMockFactory;

    function let()
    {
        $this->productMockFactory = new ProductMockFactory();
        $this->variantMockFactory = new ProductVariantMockFactory();
        $this->feedPreprocessorEntryMockFactory = new FeedPreprocessorEntryMockFactory();
        $this->exportProductMockFactory = new ExportProductMockFactory();
    }

    public function it_should_create_entities_for_variants_that_should_be_visible(
        FactoryInterface $decoratedFactory,
        FeedPreprocessorEntryReader $feedPreprocessorReader
    ): void {
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
        $expectedVariants = [
            $this->exportProductMockFactory->create($variants['SW100.1'], ['filterAttributes' => $this->getFilterAttributes($variants['SW100.1']->getProductNumber(), $variantsData)]),
            $this->exportProductMockFactory->create($variants['SW100.5'], ['filterAttributes' => $this->getFilterAttributes($variants['SW100.5']->getProductNumber(), $variantsData)]),
            $this->exportProductMockFactory->create($variants['SW100.7'], ['filterAttributes' => $this->getFilterAttributes($variants['SW100.7']->getProductNumber(), $variantsData)]),
            $this->exportProductMockFactory->create($variants['SW100.10'], ['filterAttributes' => $this->getFilterAttributes($variants['SW100.10']->getProductNumber(), $variantsData)]),
            $this->exportProductMockFactory->create($variants['SW100.13'], ['filterAttributes' => $this->getFilterAttributes($variants['SW100.13']->getProductNumber(), $variantsData)]),
            $this->exportProductMockFactory->create($variants['SW100.16'], ['filterAttributes' => $this->getFilterAttributes($variants['SW100.16']->getProductNumber(), $variantsData)]),
        ];
        $productEntity->setChildren(new ProductCollection(array_reduce($variants, fn(array $carriedVariants, ProductEntity $product): array => $carriedVariants + [$product->getId() => $product], [])));

        // Expect
        $feedPreprocessorReader->read($productEntity)->willReturn($this->getFeedPreprocessorEntries([
            $variants['SW100.1'],
            $variants['SW100.5'],
            $variants['SW100.7'],
            $variants['SW100.10'],
            $variants['SW100.13'],
            $variants['SW100.16'],
        ], $variantsData));
        $decoratedFactory->createEntities($productEntity, ExportProductEntity::class)->willReturn(new \ArrayIterator(array_map(fn(ProductEntity $variant) => $this->exportProductMockFactory->create($variant), $variants)));
        $this->beConstructedWith($decoratedFactory, $feedPreprocessorReader);

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
    private function getFeedPreprocessorEntries(array $expectedVariants, array $variantsData): array
    {
        return array_reduce(
            $expectedVariants,
            fn(array $acc, ProductEntity $variant): array => $acc + [$variant->getProductNumber() => $this->feedPreprocessorEntryMockFactory->create($variant, ['filterAttributes' => $this->getFilterAttributes($variant->getProductNumber(), $variantsData)])],
            []
        );
    }

    private function getFilterAttributes(string $variantProductNumber, array $variantsData): string
    {
        $variantData = $variantsData[$variantProductNumber];

        return sprintf('size=S|size=M|size=L|color=%s|material=%s', $variantData['color'], $variantData['material']);
    }
}
