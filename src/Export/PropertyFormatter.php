<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Export;

use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity as Option;

class PropertyFormatter
{
    /** @var FilterInterface */
    private $filter;

    public function __construct(FilterInterface $filter)
    {
        $this->filter = $filter;
    }

    public function __invoke(Option $option): string
    {
        return $this->format($option->getGroup()->getName(), $option->getName());
    }

    public function format(string ...$parts): string
    {
        return implode('=', array_map([$this->filter, 'filterValue'], $parts));
    }

    public function mapper(): \Closure
    {
        return \Closure::fromCallable($this);
    }
}
