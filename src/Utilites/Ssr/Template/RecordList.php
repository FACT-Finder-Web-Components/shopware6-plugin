<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Template;

use Omikron\FactFinder\Shopware6\Utilites\Ssr\SearchAdapter;

class RecordList
{
    private const RECORD_PATTERN     = '#<ff-record[\s>].*?</ff-record>#s';
    private const SSR_RECORD_PATTERN = '#<ssr-record-template>.*?</ssr-record-template>#s';

    private Engine $mustache;
    private SearchAdapter $searchAdapter;
    private string $salesChannelId;
    private string $content;
    private string $template;

    public function __construct(
        Engine $mustache,
        SearchAdapter $searchAdapter,
        string $salesChannelId,
        string $content
    ) {
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
    ) {
        $results        = $this->searchAdapter->search($paramString, $isNavigationRequest, $this->salesChannelId);
        $recordsContent = array_reduce(
            $results['records'] ?? [],
            fn (string $carry, array $record) => sprintf(
                '%s%s',
                $carry,
                $this->mustache->render($this->template, $this->convertRecord($record))
            ),
            ''
        );

        $this->content = str_replace('{FF_SEARCH_RESULT}', json_encode($results) ?: '', $this->content);

        return preg_replace(self::SSR_RECORD_PATTERN, $recordsContent, $this->content);
    }

    private function convertRecord(array $record): array
    {
        $record['masterValues'] = array_merge($record['masterValues'], $this->getVariant($record));

        return $record;
    }

    private function getVariant($record): array
    {
        $variantValues = $record['variantValues'] ?? [];

        if ($variantValues === []) {
            return [];
        }

        $keys = array_keys($variantValues);
        $masterKey = array_filter($keys, fn ($key) => $variantValues[$key]['isMaster'] ?? false === 'true')[0] ?? $keys[0] ?? null;

        if ($masterKey === null) {
            return [];
        }

        return $variantValues[$masterKey] ?? [];
    }

    private function setTemplateString(): void
    {
        preg_match(self::RECORD_PATTERN, $this->content, $match);

        $this->template = $match[0] ?? '';
    }
}
