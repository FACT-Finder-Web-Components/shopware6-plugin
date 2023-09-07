<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export\Filter;

class TextFilter implements FilterInterface
{
    public function filterValue(?string $value): string
    {
        // phpcs:ignore
        $tags  = '#<(address|article|aside|blockquote|br|canvas|dd|div|dl|dt|fieldset|figcaption|figure|footer|form|h[1-6]|header|hr|li|main|nav|noscript|ol|p|pre|section|table|tfoot|ul|video)#';
        $value = $value ?? '';
        $value = preg_replace($tags, ' <$1', $value); // Add one space in front of block elements before stripping tags
        $value = strip_tags($value);
        $value = htmlentities($value);
        $value = html_entity_decode($value);
        $value = preg_replace('#\s+#', ' ', $value);
        return trim($value);
    }
}
