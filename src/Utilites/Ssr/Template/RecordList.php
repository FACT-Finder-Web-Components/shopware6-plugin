<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Template;

use Omikron\FactFinder\Shopware6\Config\Communication;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\Exception\DetectRedirectException;
use Omikron\FactFinder\Shopware6\Utilites\Ssr\SearchAdapter;
use Symfony\Component\HttpFoundation\Request;

class RecordList
{
    private const RECORD_PATTERN     = '#<ff-record[\s>].*?</ff-record>#s';
    private const SSR_RECORD_PATTERN = '#<ssr-record-template>.*?</ssr-record-template>#s';

    public function __construct(
        private readonly Request       $request,
        private readonly Engine        $handlebars,
        private readonly SearchAdapter $searchAdapter,
        private readonly Communication $pluginConfig,
        private readonly string        $salesChannelId,
        private string                 $content,
        private string                 $template,
    ) {
        $this->setTemplateString();
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @throws DetectRedirectException
     */
    public function getContent(
        string $paramString,
        bool $isNavigationRequest = false,
    ): string {
        $results = $this->searchResults($paramString, $isNavigationRequest);

        // Support redirect campaigns for SSR
        if ($this->getRedirectCampaign($results)) {
            throw new DetectRedirectException($this->getRedirectCampaign($results));
        }

        // Support redirect to PDP if found one result
        if ($this->pluginConfig->isSsrPdpEnabled()) {
            if ($this->shouldRedirectToPDP($results) && $this->getProductDeeplink($results)) {
                throw new DetectRedirectException($this->getProductDeeplink($results));
            }
        }

        return $this->renderResults($results, $paramString);
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function searchResults(
        string $paramString,
        bool $isNavigationRequest = false,
    ): array {
        $paramString = strpos($paramString, 'p=') === 0
            ? sprintf('page=%s', substr($paramString, 2))
            : str_replace('&p=', '&page=', $paramString);

        return $this->searchAdapter->search($paramString, $isNavigationRequest, $this->salesChannelId);
    }

    public function renderResults(
        array $results,
        string $paramString,
    ): string {
        $records        = $results['records'] ?? [];
        $recordsContent = array_reduce(
            $records,
            fn (string $carry, array $record) => sprintf(
                '%s%s',
                $carry,
                $this->handlebars->render($this->template, $record)
            ),
            ''
        );

        $this->content = str_replace('{FF_SEARCH_RESULT}', json_encode($results) ?: '{}', $this->content);
        $this->setContentWithLinks($results, $paramString);

        if ($records === []) {
            return $this->content;
        }

        return preg_replace(self::SSR_RECORD_PATTERN, $recordsContent, $this->content);
    }

    private function setContentWithLinks(array $results, string $paramString): void
    {
        $nextPage     = $results['paging']['nextLink']['number'] ?? null;
        $previousPage = $results['paging']['previousLink']['number'] ?? null;
        $nextLink     = '';
        $previousLink = '';
        $pos          = strpos($this->request->getUri(), '?');
        $baseUrl      = $pos === false ? $this->request->getUri() : substr($this->request->getUri(), 0, $pos);
        $params       = array_filter(explode('&', $paramString), fn (string $param) => strpos($param, 'page=') !== 0);

        if ($previousPage !== null) {
            $previousLink = sprintf('<link rel="prev" href="%s?%s" />', $baseUrl, implode('&', [...$params, sprintf('page=%s', $previousPage)]));
        }

        if ($nextPage !== null) {
            $nextLink = sprintf('<link rel="next" href="%s?%s" />', $baseUrl, implode('&', [...$params, sprintf('page=%s', $nextPage)]));
        }

        $this->content = str_replace('</head>', sprintf('%s%s</head>', $previousLink, $nextLink), $this->content);
    }

    private function setTemplateString(): void
    {
        preg_match(self::RECORD_PATTERN, $this->content, $match);

        $this->template = $match[0] ?? '';
    }

    private function getRedirectCampaign(array $results): ?string
    {
        if (!empty($results['campaigns'])) {
            $campaign = array_search('REDIRECT', array_column($results['campaigns'], 'flavour'));

            return $results['campaigns'][$campaign]['target']['destination'] ?? null;
        }

        return null;
    }

    private function shouldRedirectToPDP(array $results): bool
    {
        return count($results['records']) === 1 && $results['totalHits'] === 1;
    }

    private function getProductDeeplink(array $results): ?string
    {
        return $results['records'][0]['record']['Deeplink'] ?? null;
    }
}
