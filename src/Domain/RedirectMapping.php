<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Domain;

use Exception;

class RedirectMapping
{
    private string $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function __toString(): string
    {
        $value = $this->getValue();

        return $value === [] ? '{}' : json_encode($value);
    }

    public function getValue(): array
    {
        if ($this->data === '') {
            return [];
        }

        return array_reduce(
            explode(PHP_EOL, $this->data),
            fn ($acc, $item): array => $acc + $this->getMappedItem($item),
            []
        );
    }

    private function getMappedItem(string $item): array
    {
        $item              = trim($item);
        $separatorPosition = strpos($item, '=');

        if ($separatorPosition === false) {
            return [];
        }

        $query = substr($item, 0, $separatorPosition);

        if ($query === '') {
            return [];
        }

        $url = substr($item, strlen($query) + 1, strlen($item));

        try {
            $this->validateUrl($url);
        } catch (\Exception $e) {
            return [];
        }

        return [$query => $url];
    }

    private function validateUrl(string $url): void
    {
        if ($url === '') {
            throw new Exception('Invalid url - empty string');
        }

        if (strpos($url, 'http') === 0 || strpos($url, '/') === 0) {
            return;
        }

        throw new Exception('Invalid url - url should start with "/" or "http"');
    }
}
