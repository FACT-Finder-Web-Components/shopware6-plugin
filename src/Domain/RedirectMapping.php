<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Domain;

class RedirectMapping
{
    private string $data;

    public function __construct(string $data)
    {
        $this->data = $data;
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

    public function __toString(): string
    {
        $value = $this->getValue();

        return $value === [] ? '{}' : json_encode($value);
    }

    private function getMappedItem(string $item): array
    {
        $item = trim($item);
        $separatorPosition = strpos($item, '=');

        if ($separatorPosition === false) {
            return [];
        }

        $query = substr($item, 0, $separatorPosition);

        if ($query === '') {
            return [];
        }

        $url = substr($item, strlen($query) + 1, strlen($item));

        if ($url === '') {
            return [];
        }

        return [$query => $url];
    }
}
