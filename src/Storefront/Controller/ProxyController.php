<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Storefront\Controller;

use Omikron\FactFinder\Communication\Credentials;
use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\SearchAdapter;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Template\Engine;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Template\RecordList;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class ProxyController extends StorefrontController
{
    private GenericPageLoader $pageLoader;
    private Communication $config;

    public function __construct(
        Communication $config,
        GenericPageLoader $pageLoader
    ) {
        $this->pageLoader = $pageLoader;
        $this->config     = $config;
    }

    /**
     * @Route(path="/fact-finder", name="frontend.factfinder.proxy.execute", methods={"GET", "POST"})
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function execute(
        Request $request,
        SalesChannelContext $context,
        SearchAdapter $searchAdapter,
        Engine $mustache
    ): Response {
        $uri = $request->getUri();
        $query = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);


//        $url = $this->_url->getCurrentUrl();
//
//        // Extract API name from path
//        $endpoint = $this->getEndpoint($url);
//        if (!$endpoint) {
//            throw new NotFoundException(__('Endpoint missing'));
//        }
//
//        try {
//            $client = $this->clientBuilder
//                ->withServerUrl($this->config->getServerUrl())
//                ->withCredentials(new Credentials(...$this->config->getCredentials()))
//                ->withVersion($this->config->getVersion())
//                ->build();
//
//            $method = $this->getRequest()->getMethod();
//            switch ($method) {
//                case 'GET':
//                    $query = (string) parse_url($url, PHP_URL_QUERY); // phpcs:ignore
//                    return $this->returnJson($client->request('GET', $endpoint . '?' . $query));
//                case 'POST':
//                    return $this->returnJson($client->request('POST', $endpoint, [
//                        'body'    => $this->getRequest()->getContent(),
//                        'headers' => ['Content-Type' => 'application/json'],
//                    ]));
//                default:
//                    throw new LocalizedException(__(sprintf('HTTP Method %s is not supported', $method)));
//            }
//        } catch (ClientExceptionInterface $e) {
//            return $this->rawResultFactory->create()->setContents($e->getMessage());
//        }

        return new JsonResponse();
    }
}
