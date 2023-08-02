<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Utilites\Ssr\Template;

use Omikron\FactFinder\Shopware6\Export\Filter\FilterInterface;

class Loader implements \Mustache_Loader
{
    private \Mustache_Loader $loader;
    private FilterInterface $filter;

    public function __construct(
        \Mustache_Loader $loader,
        FilterInterface $filter
    ) {
        $this->loader = $loader;
        $this->filter = $filter;
    }

    /**
     * {@inheritDoc}
     */
    public function load($name)
    {
        $template = $this->loader->load($name);
        return $template instanceof \Mustache_Source ? $template : $this->filter->filterValue($template);
    }
}
