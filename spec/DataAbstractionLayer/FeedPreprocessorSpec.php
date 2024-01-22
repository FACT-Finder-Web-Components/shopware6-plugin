<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\DataAbstractionLayer;

use Omikron\FactFinder\Shopware6\Export\Field\CustomFields as ExportCustomFields;
use Omikron\FactFinder\Shopware6\Export\Filter\TextFilter;
use Omikron\FactFinder\Shopware6\Export\PropertyFormatter;
use Omikron\FactFinder\Shopware6\TestUtil\ProductMockFactory;
use Omikron\FactFinder\Shopware6\TestUtil\ProductVariantMockFactory;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
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

class FeedPreprocessorSpec extends ObjectBehavior
{
    private ProductMockFactory $productMockFactory;
    private ProductVariantMockFactory $variantMockFactory;
    private Collaborator $customFields;
    private Context $context;

    public function let(EventDispatcherInterface $eventDispatcher, ExportCustomFields $customFields): void
    {
        $eventDispatcher->dispatch(Argument::any())->willReturn(new Event());
        $this->beConstructedWith(new PropertyFormatter(new TextFilter()), $eventDispatcher, $customFields);
        $this->context            = new Context(new SystemSource());
        $this->productMockFactory = new ProductMockFactory();
        $this->variantMockFactory = new ProductVariantMockFactory();
        $this->customFields       = $customFields;
    }

    public function it_should_export_only_one_entry_if_main_variant_is_selected(): void
    {
        $productEntity = $this->productMockFactory->create();
        $productEntity->setVariantListingConfig(new VariantListingConfig(null, 'SW100', null));
        $customFieldsData = [
            'SW100.1' => ['shape' => 'triangle', 'pattern' => 'stripes'],
            'SW100.2' => ['shape' => 'rectangle', 'pattern' => 'patterned'],
            'SW100.3' => ['shape' => 'square', 'pattern' => 'dots'],
        ];

        $customFieldsValues = [
            'SW100.1' => '|shape=triangle|pattern=stripes|',
            'SW100.2' => '|shape=rectangle|pattern=patterned|',
            'SW100.3' => '|shape=square|pattern=dots|',
        ];

        $variants = [
            $this->variantMockFactory->create(
                $productEntity,
                [
                    'productNumber' => 'SW100.1',
                    'size'          => 'S',
                    'color'         => 'red',
                    'material'      => 'cotton',
                    'customFields'  => $customFieldsData['SW100.1'],
                ]
            ),
            $this->variantMockFactory->create(
                $productEntity,
                [
                    'productNumber' => 'SW100.2',
                    'size'          => 'M',
                    'color'         => 'red',
                    'material'      => 'linen',
                    'customFields'  => $customFieldsData['SW100.1'],
                ]
            ),
            $this->variantMockFactory->create(
                $productEntity,
                [
                    'productNumber' => 'SW100.3',
                    'size'          => 'L',
                    'color'         => 'red',
                    'material'      => 'wool',
                    'customFields'  => $customFieldsData['SW100.1'],
                ]
            ),
        ];

        foreach ($variants as $variant) {
            $this->customFields->getValue($variant)->willReturn($customFieldsValues[$variant->getProductNumber()]);
        }

        $productEntity->setChildren(
            new ProductCollection(
                array_reduce($variants, fn (array $carriedVariants, ProductEntity $product): array => $carriedVariants + [$product->getId() => $product], [])));

        /** @var Subject $entries */
        $entries = $this->createEntries($productEntity, $this->context);
        $entries->shouldBeArray();
        // only one entry should be producted
        $entries->shouldHaveCount(1);

        $this->validateFilterAttributes($entries->getWrappedObject(), [
            'size=S|size=M|size=L|color=red|material=cotton|material=linen|material=wool',
        ]);
    }

    private function validateFilterAttributes(array $entries, array $filters): void
    {
        $expected           = count($filters);
        $isContainingFilter = fn (string $filter): callable => fn (array $entry): bool => strpos($entry['filterAttributes'], $filter) !== false;

        $match = array_reduce($filters, fn (int $score, string $filter) => $score + (int) array_filter($entries, $isContainingFilter($filter)), 0);

        Assert::assertEquals($expected, $match);
    }
}
