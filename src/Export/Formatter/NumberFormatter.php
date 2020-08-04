<?php

namespace Omikron\FactFinder\Shopware6\Export\Formatter;

/**
 * @api
 */
class NumberFormatter
{
    public function format(float $number, int $precision = 2): string
    {
        return sprintf("%.{$precision}F", round($number, $precision));
    }
}
