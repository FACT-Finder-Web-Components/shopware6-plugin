<?php

declare(strict_types=1);

namespace spec\Omikron\FactFinder\Shopware6\Storefront\Controller;

use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\SearchAdapter;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Template\Engine;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\Adapter\Twig\TemplateFinder;
use Shopware\Core\Framework\Event\NestedEventDispatcher;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Framework\Routing\RequestTransformer;
use Shopware\Storefront\Page\GenericPageLoader;
use Shopware\Storefront\Page\Page;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ResultControllerSpec extends ObjectBehavior
{
    private Collaborator $request;
    private Collaborator $config;
    private Collaborator $pageLoader;
    private Collaborator $container;
    private Collaborator $salesChannelContext;
    private Collaborator $twig;
    private Collaborator $seoUrlPlaceholderHandler;

    public function let(
        Request $request,
        Communication $config,
        GenericPageLoader $pageLoader,
        ContainerInterface $container,
        RequestStack $requestStack,
        SalesChannelContext $salesChannelContext,
        ParameterBag $attributes,
        TemplateFinder $templateFinder,
        NestedEventDispatcher $nestedEventDispatcher,
        SystemConfigService $systemConfigService,
        Environment $twig,
        SeoUrlPlaceholderHandlerInterface $seoUrlPlaceholderHandler
    ) {
        $this->request = $request;
        $this->config = $config;
        $this->pageLoader = $pageLoader;
        $this->container = $container;
        $this->salesChannelContext = $salesChannelContext;
        $this->twig = $twig;
        $this->seoUrlPlaceholderHandler = $seoUrlPlaceholderHandler;
        $this->beConstructedWith($config, $pageLoader);
        $requestStack->getCurrentRequest()->willReturn($request);
        $container->get('request_stack')->willReturn($requestStack);
        $this->request->attributes = $attributes;
        $attributes->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT)->willReturn($salesChannelContext);
        $attributes->get(RequestTransformer::STOREFRONT_URL)->willReturn('https://shop.com');
        $templateFinder->find('@Parent/storefront/page/factfinder/result.html.twig')->willReturn('@OmikronFactFinder/storefront/page/factfinder/result.html.twig');
        $this->container->get(TemplateFinder::class)->willReturn($templateFinder);
        $this->container->get('event_dispatcher')->willReturn($nestedEventDispatcher);
        $this->container->get(SystemConfigService::class)->willReturn($systemConfigService);
        $this->container->get(SeoUrlPlaceholderHandlerInterface::class)->willReturn($seoUrlPlaceholderHandler);
        $this->setTwig($twig);
        $nestedEventDispatcher->dispatch(Argument::any())->willReturn(Argument::any());
        $this->setContainer($container);
    }

    public function it_should_return_original_response_content_when_ssr_is_not_active(
        SearchAdapter $searchAdapter,
        Page $page,
        Engine $mustache,
        TemplateFinder $templateFinder
    ) {
        $content = 'original content';
        $this->pageLoader->load($this->request, $this->salesChannelContext)->willReturn($page);
        $this->config->isSsrActive()->willReturn(false);
        $this->twig->render('@OmikronFactFinder/storefront/page/factfinder/result.html.twig', Argument::any())->willReturn($content);
        $this->seoUrlPlaceholderHandler->replace($content, 'https://shop.com', $this->salesChannelContext)->willReturn($content);
        $response = $this->result(
            $this->request,
            $this->salesChannelContext,
            $searchAdapter,
            $this->twig,
            $templateFinder,
            $mustache
        );

        $response->shouldBeAnInstanceOf(Response::class);
        $response->getContent()->shouldReturn($content);
    }
}
