<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Template;

use Omikron\FactFinder\Shopware6\Utilites\Ssr\SearchAdapter;
use Symfony\Component\HttpFoundation\Request;

class RecordList
{
    private const RECORD_PATTERN     = '#<ff-record[\s>].*?</ff-record>#s';
    private const SSR_RECORD_PATTERN = '#<ssr-record-template>.*?</ssr-record-template>#s';

    private Request $request;
    private Engine $mustache;
    private SearchAdapter $searchAdapter;
    private string $salesChannelId;
    private string $content;
    private string $template;

    public function __construct(
        Request $request,
        Engine $mustache,
        SearchAdapter $searchAdapter,
        string $salesChannelId,
        string $content
    ) {
        $this->request        = $request;
        $this->mustache       = $mustache;
        $this->searchAdapter  = $searchAdapter;
        $this->salesChannelId = $salesChannelId;
        $this->content        = $content;
        $this->setTemplateString();
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getContent(
        string $paramString,
        bool $isNavigationRequest = false
    ): string {
        $results = $this->searchResults($paramString, $isNavigationRequest);

        return $this->renderResults($results, $paramString);
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function searchResults(
        string $paramString,
        bool $isNavigationRequest = false
    ): array {
        $paramString = strpos($paramString, 'p=') === 0
            ? sprintf('page=%s', substr($paramString, 2))
            : str_replace('&p=', '&page=', $paramString);

        return $this->searchAdapter->search($paramString, $isNavigationRequest, $this->salesChannelId);
    }

    public function renderResults(
        array $results,
        string $paramString
    ): string {
        $records        = $results['records'] ?? [];
        $recordsContent = array_reduce(
            $records,
            fn (string $carry, array $record) => sprintf(
                '%s%s',
                $carry,
                $this->mustache->render($this->template, $record)
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
}
