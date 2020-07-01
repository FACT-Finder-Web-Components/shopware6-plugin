<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Field;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity as Product;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;

class Deeplink implements FieldInterface
{
    /** @var SeoUrlPlaceholderHandlerInterface */
    private $urlPlaceholderHandler;

    public function __construct(SeoUrlPlaceholderHandlerInterface $urlPlaceholderHandler)
    {
        $this->urlPlaceholderHandler = $urlPlaceholderHandler;
    }

    public function getName(): string
    {
        return 'Deeplink';
    }

    public function getValue(Product $product): string
    {
        return $this->urlPlaceholderHandler->generate('frontend.detail.page', ['productId' => $product->getId()]);
    }
}
