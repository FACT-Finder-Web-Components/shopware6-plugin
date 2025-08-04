<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Storefront\Controller;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class AddToCartController extends StorefrontController
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly EntityRepository $productRepository,
    ) {
    }

    #[Route(
        '/fact-finder/cart/add-by-number/{productNumber}',
        name: 'frontend.cart.add.by.number',
        defaults: ['XmlHttpRequest' => true],
        methods: ['GET', 'POST']
    )]
    public function addToCartByNumber(
        string $productNumber,
        Request $request,
        SalesChannelContext $context,
    ): RedirectResponse {
        $referer  = $request->headers->get('referer');
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productNumber', $productNumber));
        $criteria->setLimit(1);

        /** @var SalesChannelProductEntity|null $product */
        $product = $this->productRepository->search($criteria, $context->getContext())->first();

        if (!$product) {
            $this->addFlash('danger', "Invalid product number: $productNumber");

            return $this->redirectToRoute('frontend.home.page');
        }

        $lineItem = (new LineItem($product->getId(), LineItem::PRODUCT_LINE_ITEM_TYPE, $product->getId(), 1))
            ->setStackable(true)
            ->setRemovable(true);
        $cart = $this->cartService->getCart($context->getToken(), $context);
        $this->cartService->add($cart, $lineItem, $context);
        $this->addFlash('success', "Product {$product->getName()} was added to the cart successfully.");

        return $this->redirectToRefererOrToHomePage($referer);
    }

    private function redirectToRefererOrToHomePage(?string $referer): RedirectResponse
    {
        if ($referer && filter_var($referer, FILTER_VALIDATE_URL)) {
            return new RedirectResponse($referer);
        }

        return $this->redirectToRoute('frontend.home.page');
    }
}
