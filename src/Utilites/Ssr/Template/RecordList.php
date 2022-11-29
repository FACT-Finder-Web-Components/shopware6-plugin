<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Template;

use Omikron\FactFinder\Shopware6\Utilites\Ssr\SearchAdapter;
use Twig\Environment;

class RecordList
{
    private const RECORD_PATTERN     = '#<ff-record[\s>].*?</ff-record>#s';
    private const SSR_RECORD_PATTERN = '#<ssr-record-template>.*?</ssr-record-template>#s';

    private Environment $twig;
    private SearchAdapter $searchAdapter;
    private string $content;
    private string $template;

    public function __construct(
        Environment $twig,
        SearchAdapter $searchAdapter,
        string $content
    ) {
        $this->twig          = $twig;
        $this->searchAdapter = $searchAdapter;
        $this->content       = $content;
        $this->setTemplateString();
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getContent(
        string $paramString,
        bool $isNavigationRequest = false
    ) {
        $results        = $this->searchAdapter->search($paramString, $isNavigationRequest);
        $template       = $this->twig->createTemplate($this->template);
        $recordsContent = array_reduce(
            $results['records'] ?? [],
            fn (string $carry, array $record) => sprintf(
                '%s%s',
                $carry,
                $template->render($record)
            ),
            ''
        );

        $this->content = str_replace('{FF_SEARCH_RESULT}', json_encode($results) ?: '', $this->content);

        return preg_replace(self::SSR_RECORD_PATTERN, $recordsContent, $this->content);
    }

    private function setTemplateString(): void
    {
        preg_match(self::RECORD_PATTERN, $this->content, $match);

        $this->template = $match[0] ?? '';
    }
}
