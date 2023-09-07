<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Filter;

class ExtendedTextFilter extends TextFilter
{
    private const FORBIDDEN_CHARS = '/[|#=]/';

    public function filterValue(?string $value): string
    {
        $value = $value ?? '';
        return trim(preg_replace(self::FORBIDDEN_CHARS, ' ', parent::filterValue($value)));
    }
}
